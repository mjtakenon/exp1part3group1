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

  for (let i = 0; i < height; i++)
    for (let j = 0; j < width; j++)
      $('.y' + i + ' > .x' + j).addClass('empty-cell', 3000, 'linear');
}

function setImage(x, y, url) {
  const e = $('.y' + y + ' > .x' + x);
  e.removeClass('empty-cell', 1500, 'linear', function () { // 消えるアニメーション
    // 追加する部分のCSS
    e.css({
      background: 'url(' + url + ')',
      'background-size': 'cover',
      opacity: 0
    });
    // 追加部分のアニメーション
    e.animate({
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
