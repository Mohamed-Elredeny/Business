$(function () {
    var languages = $('.languages');
    var plans = $('.plans')
    if (languages.length) {
        languages.owlCarousel({
            items: 4,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayHoverPause: true,
            autoplayTimeout: 5000,
            nav: false,
            smartSpeed: 2000,
            margin: 20,
            responsive: {
                0: {
                    items: 2
                },
                577: {
                    items: 3,
                },
                991: {
                    items: 4,
                },
                1200: {
                    items: 4,
                }
            },
        });
    }
    if (plans.length) {
        plans.owlCarousel({
            items: 3,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayHoverPause: true,
            autoplayTimeout: 5000,
            nav: false,
            smartSpeed: 2000,
            margin: 20,
            responsive: {
                0: {
                    items: 1
                },
                577: {
                    items: 2,
                },
                991: {
                    items: 3,
                },
                1200: {
                    items: 3,
                }
            },
        });
    }

    $('.signup-form').submit(function (e) {
        e.preventDefault();
        var first_name = $('.firstName').val()
        var last_name = $('.lastName').val()
        var email = $('.email').val()
        var password = $('.password').val()
        var confirmPassword = $('.passwordConfirm').val()

        $(".error").remove()

        if (first_name.length < 1) {
            $('.firstName').parent().after('<p class="error">This field is required</p>')
        }
        if (last_name.length < 1) {
            $('.lastName').parent().after('<p class="error">This field is required</p>')
        }
        if (email.length < 1) {
            $('.email').parent().after('<p class="error">This field is required</p>')
        } else {
            var regEx = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i
            var validEmail = regEx.test(email);
            if (!validEmail) {
                $('.email').parent().after('<p class="error">Enter a valid email</p>')
            }
        }
        if (password.length < 8) {
            $('.password').parent().after('<p class="error">Password must be at least 8 characters long</p>')
        }
        if (password !== confirmPassword || confirmPassword == '') {
            $('.passwordConfirm').parent().after('<p class="error">password confirm not match with password</p>')
        }
    })
    $('.login-form').submit(function (e) {
        e.preventDefault()
        var email = $('.email').val()
        var password = $('.password').val()

        $(".error").remove()

        if (email.length < 1) {
            $('.email').parent().after('<p class="error">This field is required</p>')
        } else {
            var regEx = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i
            var validEmail = regEx.test(email)
            if (!validEmail) {
                $('.email').parent().after('<p class="error">Enter a valid email</p>')
            }
        }
        if (password.length < 8) {
            $('.password').parent().after('<p class="error">Password must be at least 8 characters long</p>')
        }
    })


    var navIsOpen = true

    function closeProcess() {
        $('.side-navigation').animate({width: '0'}, 1000)
        $('.side-navigation .nav-list').animate({opacity: '0'}, 500)
        $('.side-navigation .nav-link').css('display', 'none')
        $('.sidnav-brand').css('display', 'none')
        $('.close-icon').hide(1000)
        $('.toggler-icon').show(1000)
        navIsOpen = false
    }

    $('.toggler-icon').hide()

    $('.toggler-icon').on('click', function () {
        $('.side-navigation').animate({width: '25rem'}, 1000)
        $('.side-navigation .nav-list').animate({opacity: '1'}, 500)
        $('.side-navigation .nav-link').css('display', 'inline-block')
        $('.sidnav-brand').css('display', 'block')
        $(this).hide(1000)
        $('.close-icon').show(1000)
        navIsOpen = true
    })

    $('.close-icon').on('click', function () {
        closeProcess()
    })

    // $('.nav-link').on('click', function () {
    //   closeProcess()
    // })

    function addCurrent(selector) {
        $(selector).on('click', function () {
            $(selector).each(function () {
                $(this).removeClass('current')
            })
            $(this).addClass('current')
        })
    }

    addCurrent('.navbar-nav .nav-link')
    addCurrent('.side-navigation .nav-link')


    $("#upload_btn").on('click', function (e) {

        e.preventDefault();
        if ($("#upload_btn").attr('class') != 'save-button') {
            $("#image_file").trigger('click');
        } else {

            $("#profileform").submit();
        }

    });

    $("#image_file").on('change', function () {

        let image_value = $(this).val();

        $("#upload_btn").html("<i class='fas fa-cloud-upload-alt'></i> save")
        $("#upload_btn").attr('class', 'save-button')
        $("#cancel").css('display', 'inline')
    });


    $("#cancel_button").on('click', function (e) {

        $("#cancel_form").submit();

    });


    $("#translator_upload_btn").on('click', function (e) {

        e.preventDefault();
        if ($("#translator_upload_btn").attr('class') != 'save-button') {
            $("#translator_file").trigger('click');
        } else {

            $("#translatorform").submit();
        }

    });

    $("#translator_file").on('change', function () {

        let image_value = $(this).val();

        $("#translator_upload_btn").html("<i class='fas fa-cloud-upload-alt'></i> Save")
        $("#translator_upload_btn").attr('class', 'save-button')
    });


    $('.custom-select-from').change(function () {
        if ($('.custom-select-from option:selected').attr('label') === 'arabic' ) {
            $('.custom-select-to option').each(function () {
                $(this).removeAttr('disabled selected')
            })
            $('.custom-select-to option[label="arabic"]').attr('disabled', 'disabled')
            $('textarea#textToTranslate').css('direction', 'rtl')
        } else {
            $('.custom-select-to option').each(function () {
                $(this).attr('disabled', 'disabled')
            })
            $('.custom-select-to option[label="arabic"]').removeAttr('disabled selected')
            $('.custom-select-to option[label="arabic"]').attr('selected', 'selected')
            $('textarea#textToTranslate').css('direction', 'ltr')
        }

        if ($('.custom-select-from option:selected').attr('label') === 'chinese') {
            $('.wordsCount').parent().hide()
            $('.valueCharsHiddenInput').attr('name', 'words')
            $('.valueWordsHiddenInput').attr('name', 'chars')
        } else {
            $('.wordsCount').parent().show()
        }
    })

    $('#textToTranslate').keyup(function () {
        let charsCount = $(this)
            .val()
            .replace(/[`~!@#$%^&*()_|+\-=?;:؟ـ‘'",.،؛<>«»\{\}\[\]\\\/]/gi, '')
            .split(/\s+/)
            .filter(v => v != '' && v != '\n')
            .join('')
            .length

        let wordsCount = $(this)
            .val()
            .replace(/[`~!@#$%^&*()_|+\-=?;:؟ـ‘'",.،؛<>«»\{\}\[\]\\\/]/gi, '')
            .split(/\s+/)
            .filter(v => v != '' && v != '\n')
            .length


        $('.wordsCount').text(wordsCount)
        $('.charsCount').text(charsCount)
        $('.valueWordsHiddenInput').val(wordsCount)
        $('.valueCharsHiddenInput').val(charsCount)
    })


})
