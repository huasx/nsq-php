# nsq-php
nsq client for php
NSQ php http clinet
==============================

Add the dependency to `composer.json`:

```
"repositories": [
    {
      "type": "git",
      "url": "git@git.verystar.cn:huasx/nsq-php.git"
    },
},

....

"require": {
    "job/nsq-php": "1.*"
}
```

Run:

```
compsoer update
```

Use:

```
<?php
use Job\Nsq\Http\NsqClient;

$host = '127.0.0.1';
$port = 4151;
$topic = 'test';

$client = new NsqClient($host,$port,$topic);

$nsqData = '{ccc:ssss}';

$ret = $client->pub($nsqData);

if($ret){
//ok
}else{
//failure
}

```
