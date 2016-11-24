       	$(document).ready(function () {
            $('a.categoryName').click(function () {
                if ($(this).parent('li').hasClass("oooo"))
                    return true;
                if ($(this).parent('li').hasClass("ooo")) {
                    $(this).next('ul').slideToggle();
                    return false;
                }
            })
        });