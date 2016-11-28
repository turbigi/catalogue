       	$(document).ready(function () {
            $('a.categoryName').click(function () {
                if ($(this).parent('li').hasClass("lastChild"))
                    return true;
                if ($(this).parent('li').hasClass("parent")) {
                    $(this).next('ul').slideToggle();
                    return false;
                }
            })
        });