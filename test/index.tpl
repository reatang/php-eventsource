<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EventSrouce</title>
</head>
<body>
<pre id="output"></pre>
<script>
  var output = document.getElementById('output');

  try {
    var source    = new EventSource('/server');
    source.onopen = function () {
      output.innerHTML = '已启动';

      return;
    };
    source.onmessage = function(e) {
      console.log(e.data);
    };
    source.addEventListener('some_event_name', function (evt) {
      output.innerHTML = evt.data;

      return;
    });
  } catch (e) {
    console.log(e);
  }
</script>
</body>
</html>