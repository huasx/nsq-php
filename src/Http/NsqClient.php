<?php
/**
 * Nsq Http 客户端
 * @author ricky
 * @create 2016-10-11
 */

namespace Job\Nsq\Http;

class NsqClient
{
    private $host;
    private $port;
    private $topic;
    private $retryTimes = 0;

    /**
     * NsqClient constructor.
     *
     * @param $host       string HOST
     * @param $port       int PORT
     * @param $topic      string TOPIC
     * @param $retryTimes int 重试次数
     */
    public function __construct($host, $port, $topic = null, $retryTimes = 1)
    {
        $this->host       = $host;
        $this->port       = $port;
        $this->topic      = $topic;
        $this->retryTimes = $retryTimes;
    }

    /**
     * 一次发布单条nsq消息
     *
     * @param $nsq_datas
     * @return bool
     * @throws \Exception
     */
    public function pub($nsq_datas)
    {
        return $this->doPub($this->topic, 'pub', $nsq_datas);
    }

    public function publish($topic, $data, $defer = 0)
    {

        return $this->doPub($topic, 'pub', $data, $defer);
    }

    public function mpublish($topic, $data)
    {
        return $this->doPub($topic, 'mpub', $data);
    }

    /**
     * 一次发布多条nsq消息
     *
     * @param $nsq_datas
     * @return bool
     * @throws \Exception
     */
    public function mpub($nsq_datas)
    {
        return $this->doPub($this->topic, 'mpub', $nsq_datas);
    }

    /**
     * 执行nsq消息发布
     *
     * @param $topic
     * @param $cmd
     * @param $nsq_datas
     * @param $defer
     *
     * @return bool
     * @throws \Exception
     */
    private function doPub($topic, $cmd, $nsq_datas, $defer = 0)
    {
        if (empty($nsq_datas)) {
            throw new \InvalidArgumentException('NsqHttpClient: nsq data is empty');
        }

        if ($cmd == 'mpub') {
            $message = implode("\n", $nsq_datas);
        } else {
            $message = $nsq_datas;
        }

        $result = false;
        for ($i = 0; $i <= $this->retryTimes; $i++) {
            try {
                $curl = new Curl();
                $url  = $this->host . ':' . $this->port . "/{$cmd}?topic={$topic}";
                if ($defer) {
                    $url .= '&defer=' . $defer;
                }

                $response = $curl->post($url, $message);
                if ($result = ($response->getStatusCode() == 200)) {
                    break;
                } else {
                    throw new \Exception('post nsq message fail');
                }
            } catch (\Exception $e) {
                if ($i == $this->retryTimes) {
                    throw $e;
                }
            }
        }

        return $result;
    }
}