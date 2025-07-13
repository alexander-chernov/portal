//////////////////////////////////////////////////////////////////////////////////
/*
 * Поменять переменную, содержащую номер картинки
 * @param {int} i Номер картинки
 */
function changeImageNum(i) {
    var num = document.getElementById('count_im');
    num.setAttribute('value', i);
}

/*
 * Кнопка "Next"
 * @param {int} max Всего картинок
 */
function nextImage(max) {
    var num = document.getElementById('count_im');
    var num_val = document.getElementById('count_im').value;
    if (num_val < max) {
        num_val++;
    } else {
        num_val = 1;
    }
    num.setAttribute('value', num_val);
    changeImage(num_val);
}

/*
 * Кнопка "Prev"
 * @param {int} max Всего картинок
 */
function prevImage(max) {
    var num = document.getElementById('count_im');
    var num_val = document.getElementById('count_im').value;
    if (num_val > 1) {
        num_val--;
    } else {
        num_val = max;
    }
    num.setAttribute('value', num_val);
    changeImage(num_val);
}


/*
 * Отобразить нужную картинку
 * @param {int} i Номер картинки
 */
function changeImage(i) {
    $('.block_listing .im_2').hide();
    $('#photo_karta_' + i).show();
    changeImageNum(i);
}


function changeImage2(i) {
    $('.block_listing .im_2').hide();
    $('#photo_nedvigimost_' + i).show();
    changeImageNum(i);
}

function nextImage2(max) {
    var num = document.getElementById('count_im');
    var num_val = document.getElementById('count_im').value;
    if (num_val < max) {
        num_val++;
    } else {
        num_val = 1;
    }
    num.setAttribute('value', num_val);
    changeImage2(num_val);
}

function prevImage2(max) {
    var num = document.getElementById('count_im');
    var num_val = document.getElementById('count_im').value;
    if (num_val > 1) {
        num_val--;
    } else {
        num_val = max;
    }
    num.setAttribute('value', num_val);
    changeImage2(num_val);
}