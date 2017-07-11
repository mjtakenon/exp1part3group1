const width  = 4;
const height = 4;

function init() {
  let s = '';
  for (let i = 0; i < height; i++) {
    s += '<tr class="y' + i + '">'
    for (let j = 0; j < width; j++) {
      s += '<td class="x' + j + '"></td>';
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
}

function setImage(x, y, url) {
  const e = $('.y' + y + ' > .x' + x);
  e.removeClass('empty-cell', 1500, 'linear', function () { // 消えるアニメーション
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

$("#file_input").change(function () {
  $("#dummy_file").val($(this).val().replace("C:\\fakepath\\", ""));
});
