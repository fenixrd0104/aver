<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="renderer" content="webkit">
    <meta name="HandheldFriendly" content="True"/>
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <link rel="shortcut icon" href="./static/img/tether.svg"/>
    <title>
        USDT 在线收银台
    </title>
    <link href="./static/css/main.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="icon">
            <img class="logo" src="./static/img/tether.svg" alt="logo">
        </div>
        <h1>
            <?= $_SERVER['HTTP_HOST']; ?>
        </h1>
        <label>
            请扫描二维码或点击金额和地址粘贴转账USDT(trc-20)支付。<br> <b>转账金额必须为下方显示的金额且需要在倒计时内完成转账，否则无法被系统确认！</b>
        </label>
    </div>
    <div class="content">
        <div class="section">
            <div class="title">
                <h1 class="amount parse-amount" data-clipboard-text="<?= $usdt; ?>" id="usdt">
                    <?= $usdt; ?> <span>USDT.TRC20</span>
                </h1>
            </div>
            <div class="address parse-action" data-clipboard-text="<?= $USDT_ADDRESS; ?>" id="address">
                <?= $USDT_ADDRESS; ?>
            </div>
            <div class="main">
                <img class="qrcode"
                     src="./static/qrcode/index.php?c=<?= $USDT_ADDRESS; ?>"
                     alt="qrcode">
            </div>

            <div class="timer">
                <ul class="downcount">
                    <li>
                        <span class="hours">00</span>
                        <p class="hours_ref">时</p>
                    </li>
                    <li class="seperator">:</li>
                    <li>
                        <span class="minutes">00</span>
                        <p class="minutes_ref">分</p>
                    </li>
                    <li class="seperator">:</li>
                    <li>
                        <span class="seconds">00</span>
                        <p class="seconds_ref">秒</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>Powered by <a href="https://qzone.work/codes/668.html" target="_blank">Qzone.Work</a></p>
    </div>
</div>
<script src="./static/js/jquery.min.js"></script>
<script src="//lib.baomitu.com/layer/3.1.1/layer.js"></script>
<script src="./static/js/clipboard.min.js"></script>
<script>
    $(function () {
        (new Clipboard('#usdt')).on('success', function (e) {
            layer.msg('金额复制成功');
        });
        (new Clipboard('#address')).on('success', function (e) {
            layer.msg('地址复制成功');
        });

        // 支付时间倒计时

        function clock() {
            let timeout = new Date(<?=$valid; ?>);
            let now = new Date();
            let ms = timeout.getTime() - now.getTime();//时间差的毫秒数
            let second = Math.round(ms / 1000);
            let minute = Math.floor(second / 60);
            let hour = Math.floor(minute / 60);
            if (ms <= 0) {
                layer.alert("支付超时，请重新发起支付！", {icon: 5});
                return;
            }

            $('.hours').text(hour.toString().padStart(2, '0'));
            $('.minutes').text(minute.toString().padStart(2, '0'));
            $('.seconds').text((second % 60).toString().padStart(2, '0'));

            return setTimeout(clock, 1000);
        }

        setTimeout(clock, 1000);
    });

    // 检查是否支付完成
    function loadMsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "./status.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {trade_no: "<?php echo $row['trade_no']?>"}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 1) {
                    layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.1, time: 15000});
                    setTimeout(window.location.href = data.backurl, 1000);
                } else {
                    setTimeout(loadMsg, 5000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout(loadMsg, 1000);
                } else { //异常
                    setTimeout(loadMsg, 4000);
                }
            }
        });
    }

    window.onload = loadMsg();
</script>
</body>
</html>