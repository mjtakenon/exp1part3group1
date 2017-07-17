console.log(location.host);
//ポート番号をはじきたかったので分割
const ws = new WebSocket('ws://' + location.host.split(":")[0] + ':9000');
let tdWidth;

function init(width, height) {
  let s = '';
  for (let i = 0; i < height; i++) {
    s += '<tr class="y' + i + '">'
    for (let j = 0; j < width; j++) {
      s += '<td class="x' + j + '"><div style="display: none;"></div></td>';
    }
    s += '</tr>'
  }
  $('#mosaic > tbody').html(s);
  tdWidth = 100 / width;
  $('td').css({
    'width':  tdWidth + 'vw',
    'height': tdWidth + 'vw',
  });

  for (let i = 0; i < height; i++) {
    for (let j = 0; j < width; j++) {
      $('.y' + i + ' > .x' + j).delay().addClass('empty-cell', 500, 'linear');
    }
  }

  ws.onopen = onOpen;
  ws.onmessage = onMessage;
}

function onOpen() {
  console.log('Connection established.');
  ws.onmessage = onMessage;
}

function onMessage(event) {
  if (event && event.data) {
    const d = JSON.parse(event.data);
    console.log("get image data");
    setImage(d.x, d.y, d.url);
  }
}

function setImage(x, y, url) {
  const e = $('.y' + y + ' > .x' + x);
  e.removeClass('empty-cell', 1500, 'linear', function () { // 消えるアニメーション
    e.children('div').html(url.replace(/_s\.jpg$/, '_c.jpg')); // 大きな画像へのリンクを設定 (flickrの場合)
    e.css({ // 追加する部分のCSS
      background: 'url(' + url + ')',
      'background-size': 'cover',
      opacity: 0
    }).animate({ // 追加アニメーションの実行
      opacity: '1'
    }, 1500);
  });
}

// for debugging.
function debug() {
  setImage(2, 2, 'https://pbs.twimg.com/media/B0wx8kpCAAAfFjx.jpg');
}

// debug();

const _URL = window.URL || window.webkitURL;
let imgWidth, imgHeight;
$('#file_input').change(function () {
  $('#dummy_file').val($(this).val().replace('C:\\fakepath\\', ''));
  if ($('#dummy_file').val() !== '')
    $("#horizontal").prop("disabled", false); // #horizontal の disabled を解除

  let file, img;
  if ((file = this.files[0])) {
    img = new Image();
    img.onload = function () {
      imgWidth  = this.width;
      imgHeight = this.height;
      $('#vertical').val(+Math.round($('#horizontal').val() * (imgHeight / imgWidth)));
    };
    img.src = _URL.createObjectURL(file);
  }
});

$(document).on('keyup change', '#horizontal', function () {
  $('#vertical').val(+Math.round($('#horizontal').val() * (imgHeight / imgWidth)));
});

$('#submit_btn').on('click', function () {
  if ($('#dummy_file').val() === '') {
    alert('You need to select a file first!');
    return;
  }

  let file = $('#file_input')[0].files[0];
  let reader = new FileReader();

  reader.onload = function () {
    ws.send(JSON.stringify({ // 分割数を送信
      width:  +$('#horizontal').val(),
      height: +$('#vertical').val()
    }));
    ws.onmessage = function (event) {
      console.log(event);
      if (event && event.data === 'ACK'){
          var base64 = btoa(reader.result);
          base64 = base64.replace(/^.*,/, '');
            ws.send(base64);
      } // ACK を受信後 ファイルを送信
      init($('#horizontal').val(), $('#vertical').val());
    };
  }
  reader.readAsDataURL(file);
});

$('#mosaic > tbody').on('click', 'tr > td:not(.empty-cell)', function () {
  $('#previewModal .modal-body > img').attr('src', $(this).children('div').html());
  $('#previewModal .modal-body > a').attr('href', $(this).children('div').html());
  $('#previewModal').modal();
});

var mousewheelevent = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
$(document).on(mousewheelevent, '#mosaic', function(e){
  e.preventDefault();
  var delta = e.originalEvent.deltaY ? -(e.originalEvent.deltaY) : e.originalEvent.wheelDelta ? e.originalEvent.wheelDelta : -(e.originalEvent.detail);
  if (delta < 0){
    // マウスホイールを下にスクロールしたときの処理
    tdWidth -= 0.1;
  } else {
    // マウスホイールを上にスクロールしたときの処理
    tdWidth += 0.1;
  }

  $('td').css({
    'width':  tdWidth + 'vw',
    'height': tdWidth + 'vw',
  });
});