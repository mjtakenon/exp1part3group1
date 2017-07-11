const ws = new WebSocket('ws://' + location.host + ':9000');
const width  = 4;
const height = 4;

function init() {
  let s = '';
  for (let i = 0; i < height; i++) {
    s += '<tr class="y' + i + '">'
    for (let j = 0; j < width; j++) {
      s += '<td class="x' + j + '"><div style="display: none;"></div></td>';
    }
    s += '</tr>'
  }
  $('#mosaic > tbody').html(s);

  let delayTime = 0;
  for (let i = 0; i < height; i++) {
    for (let j = 0; j < width; j++) {
      $('.y' + i + ' > .x' + j).delay(delayTime).addClass('empty-cell', 3000, 'linear');
      delayTime += 150;
    }
  }

  ws.onopen = onOpen;
}

function onOpen() {
  console.log('Connection established.');
  ws.onmessage = onMessage;
}

function onMessage(event) {
  if (event && event.data) {
    const d = JSON.parse(event.data);
    setImage(d.x, d.y, d.url);
  }
}

function setImage(x, y, url) {
  const e = $('.y' + y + ' > .x' + x);
  e.removeClass('empty-cell', 1500, 'linear', function () { // 消えるアニメーション
    e.children('div').html(url);
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

init();
debug();

$('#file_input').change(function () {
  $('#dummy_file').val($(this).val().replace('C:\\fakepath\\', ''));
});

$('#submit_btn').on('click', function () {
  ws.send($('#file_input').get(0).files);
});

$('#mosaic > tbody').on('click', 'tr > td:not(.empty-cell)', function () {
  $('#previewModal .modal-body > img').attr('src', $(this).children('div').html());
  $('#previewModal .modal-body > a').attr('href', $(this).children('div').html());
  $('#previewModal').modal();
});