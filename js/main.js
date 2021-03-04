(function($){
    $(document).ready(function(){
        let back;

        /**
         * if user click next at page 1 next button
         */
        $(document).on('click', '#next-1', function(e){
            e.preventDefault();
            let braces = $('#booking_form input[name="braces"]:checked').val();
            if(typeof (braces) !== 'undefined'){
                $('.content').css('margin-left', '-100%');
                back = 'back-1';
                $('#booking-message1').fadeOut('fast');
            }else{
                $('#booking-message1').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                    '  <strong>Warning!</strong> Please select your answer.\n' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '</div>').fadeIn('fast');
            }
        });

        /**
         * if user click next at page 2 next button
         */
        $(document).on('click', '#next-2', function(e){
            e.preventDefault();
            let straighten = $('#booking_form input[name="straighten"]:checked').val();
            if(typeof (straighten) !== 'undefined'){
                $('.content').css('margin-left', '-200%');
                back = 'back-2';
                $('#booking-message2').fadeOut('fast');
            }else{
                $('#booking-message2').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                    '  <strong>Warning!</strong> Please select your answer.\n' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '</div>').fadeIn('fast');
            }
        });

        /**
         * if user click next at page 4 next button
         */
        $(document).on('click', '#next-3', function(e){
            e.preventDefault();
            let straightening = $('#booking_form input[name="straightening"]:checked').val();
            if(typeof (straightening) !== 'undefined'){
                $('.content').css('margin-left', '-300%');
                back = 'back-3';
                $('#booking-message3').fadeOut('fast');
            }else{
                $('#booking-message3').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                    '  <strong>Warning!</strong> Please select your answer.\n' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '</div>').fadeIn('fast');
            }

        });

        /**
         * if user click next at page 4 next button
         */
        $(document).on('click', '#next-4', function(e){
            e.preventDefault();
            let first_name = $('#booking_form input[name="first_name"]').val();
            let last_name = $('#booking_form input[name="last_name"]').val();
            if(first_name !== '' && last_name !== ''){
                $('.content').css('margin-left', '-400%');
                back = 'back-4';
                $('#booking-message4').fadeOut('fast');
            }else{
                $('#booking-message4').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                    '  <strong>Warning!</strong> All fields are required.\n' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '</div>').fadeIn('fast');
            }
        });

        /**
         * if user click next at page 5 next button
         */
        $(document).on('click', '#next-5', function(e){
            e.preventDefault();

            let email = $('#booking_form input[name="email"]').val();
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(email == ''){
                $('#booking-message5').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                    '  <strong>Warning!</strong> Email required.\n' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '</div>').fadeIn('fast');
            }else if(re.test(String(email).toLowerCase()) == false){
                $('#booking-message5').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">\n' +
                    '  <strong>Stop!</strong> Invalid email.\n' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '</div>').fadeIn('fast');
            }else{
                $('.content').css('margin-left', '-500%');
                back = 'back-5';
                $('#booking-message5').fadeOut('fast');
            }
        });

        /**
         * if user click next at page 6 next button
         */
        $(document).on('click', '#next-6', function(e){
            e.preventDefault();
            let location = $('#booking_form input[name="location"]:checked').val();
            if(typeof (location) !== 'undefined'){
                $('.content').css('margin-left', '-600%');
                back = 'back-6';
                $('#booking-message67').fadeOut('fast');
            }else{
                $('#booking-message67').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                    '  <strong>Warning!</strong> Please select location.\n' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '</div>').fadeIn('fast');
            }
        });

        /**
         * if user click next at page 7 next button
         */
        $(document).on('click', '#next-7', function(e){
            e.preventDefault();
             let event = $('#booking_form input[name="event"]:checked').val();
             if(typeof (event) !== 'undefined'){
                 $('.content').css('margin-left', '-700%');
                 back = 'back-7';
                 $('#booking-message7').fadeOut('fast');
             }else{
                 $('#booking-message7').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                     '  <strong>Warning!</strong> Please select an event.\n' +
                     '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                     '</div>').fadeIn('fast');
             }
        });

        /**
         * if user click the back button
         */
        $(document).on('click', '.back', function(e){
            e.preventDefault();
            if(back == 'back-1'){
                $('.content').css('margin-left', '0%');
            }else if(back == 'back-2'){
                $('.content').css('margin-left', '-100%');
                back = 'back-1';
            }else if(back == 'back-3'){
                $('.content').css('margin-left', '-200%');
                back = 'back-2';
            }else if(back == 'back-4'){
                $('.content').css('margin-left', '-300%');
                back = 'back-3';
            }else if(back == 'back-5'){
                $('.content').css('margin-left', '-400%');
                back = 'back-4';
            }else if(back == 'back-6'){
                $('.content').css('margin-left', '-500%');
                back = 'back-5';
            }else if(back == 'back-7'){
                $('.content').css('margin-left', '-600%');
                back = 'back-6';
            }else{
                $('.content').css('margin-left', '0%');
                back = 'back-1';
            }
        });

        /**
         * get date when user click a date
         */
        let eventDate;
        function selectDate(date) {
            $('.calendar-wrapper').updateCalendarOptions({
                date: date,
            });
            eventDate = date.slice(0, 15);
            available_time(eventDate);
        }

        /**
         * make calender
         * @type {{date: Date, weekDayLength: number, showTodayButton: boolean, onClickDate: selectDate, showYearDropdown: boolean}}
         */
        var defaultConfig = {
            weekDayLength: 2,
            date: new Date(),
            onClickDate: selectDate,
            showYearDropdown: true,
            showTodayButton: false,
        };

        $('.calendar-wrapper').calendar(defaultConfig);

        /**
         * make all date disable before today date
         */
        var prev_date = new Date();
        prev_date.setDate(prev_date.getDate() - 1);
        $('.calendar-wrapper').updateCalendarOptions({
            date: new Date(),
            disable: function(date){
                return date <= prev_date;
            }
        });

        /**
         * change referral input if user select a referral
         */
        $(document).on('change', '#booking_form select[name="referral_name"]', function(e){
            let data = $(this).val();
            if(data == 'dentist'){
                $('#referral_input label').text('Enter the name of dentist/office');
                $('#referral_input input').attr('placeholder', 'Eg: Jhon doe');
                $('#referral_input').show('fast');
            }else if(data == 'employer'){
                $('#referral_input label').text('Enter your company name');
                $('#referral_input input').attr('placeholder', 'Eg: Smiles for Seattle');
                $('#referral_input').show('fast');
            }else if(data == 'facebook' || data == 'google' || data == 'instagram' || data == 'mailer' || data == 'yelp' || data == 'friend/family'){
                $('#referral_input').hide('fast');
                $('#booking_form input[name="referred_by"]').val('');
            }else if(data == 'radio'){
                $('#referral_input label').text('Enter radio station');
                $('#referral_input input').attr('placeholder', 'Eg: 89.0FM');
                $('#referral_input').show('fast');
            }else if(data == 'wechat'){
                $('#referral_input label').text('Enter public account name');
                $('#referral_input input').attr('placeholder', 'Public account name');
                $('#referral_input').show('fast');
            }else if(data == 'event'){
                $('#referral_input label').text('Enter name of the event');
                $('#referral_input input').attr('placeholder', 'Enter name of the event');
                $('#referral_input').show('fast');
            }else if(data == 'other'){
                $('#referral_input label').text('How you found us');
                $('#referral_input input').attr('placeholder', 'How you found us');
                $('#referral_input').show('fast');
            }
        });

        /**
         * book appointment using user
         */
        if(typeof (eventDate) == 'undefined'){
            let date = new Date();
            eventDate = String(date).slice(0, 15);
            available_time(eventDate);
        }else{
            available_time(eventDate);
        }
        $(document).on('submit', '#booking_form', function(e){
           e.preventDefault();
           let braces = $('#booking_form input[name="braces"]:checked').val();
           let straighten = $('#booking_form input[name="straighten"]:checked').val();
           let straightening = $('#booking_form input[name="straightening"]:checked').val();
           let first_name = $('#booking_form input[name="first_name"]').val();
           let last_name = $('#booking_form input[name="last_name"]').val();
           let email = $('#booking_form input[name="email"]').val();
           let location = $('#booking_form input[name="location"]:checked').val();
           let event_time= $('#booking_form input[name="event"]:checked').val();
           let referral_name = $('#booking_form select[name="referral_name"]').val();
           let referred_by = $('#booking_form input[name="referred_by"]').val();
           let cell = $('#booking_form input[name="cell"]').val();
           if(referral_name == '' || cell == ''){
               $('#booking-message8').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                   '  <strong>Warning!</strong> Please put phone number and select a referral.\n' +
                   '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                   '</div>').fadeIn('fast');
           }else{
               if(typeof (eventDate) == 'undefined'){
                   let date = new Date();
                   eventDate = String(date).slice(0, 15);
               }
               $.ajax({
                   url: imitPluginData.ajax_url,
                   type: 'POST',
                   data: {action:'imit_booking', braces:braces, straighten:straighten, straightening:straightening, first_name:first_name, last_name:last_name, email:email, event_time:event_time, referral_name:referral_name, event_date:eventDate, location:location, referred_by:referred_by, cell:cell, nonce:imitPluginData.imit_nonce},
                   success:function(data){
                       $('#booking_form input[name="first_name"]').val('');
                       $('#booking_form input[name="last_name"]').val('');
                       $('#booking_form input[name="email"]').val('');
                       $('#booking_form input[name="referred_by"]').val('');
                       $('#booking_form input[name="cell"]').val('');
                       $('#booking-message8').html('<div class="alert alert-success alert-dismissible fade show" role="alert">\n' +
                           '  Your appointment has been sent successfully....<br/>\n' +
                           'Our team will confirm your booking soon.<br/>\n' +
                           'Thanks.\n' +
                           '  <button type="button" class="btn btn-success d-block" data-bs-dismiss="alert" aria-label="Close" id="form-close">Ok</button>\n' +
                           '</div>').fadeIn('fast');
                       eventDate = new Date();
                       available_time(eventDate);
                       selectDate(String(eventDate));
                   }
               });
           }
        });

        /**
         * if user click form close button then reset form
         */
        $(document).on('click', '#form-close', function(e){
            e.preventDefault();
            $('.content').css('margin-left', '0%');
        });

        /**
         * fetch available nooking time
         */
        function available_time(date){
            $('#available_time_loader').fadeIn('fast');
            $.ajax({
                url: fetchPluginDate.ajax_url,
                type: 'POST',
                data: {action:'imit_available_time', date:date, nonce:fetchPluginDate.imit_nonce},
                success:function(data){
                    $('#available_time_loader').fadeOut('fast');
                    $('#fetch_available_time').html(data);
                }
            });
        }

        /**
         * event live check
         */
        $(document).on('submit', '#appointment_check', function(e){
            e.preventDefault();
            $('#booking_status').fadeOut('fast');
            $('#booking_status_spinner').fadeIn('fast');
            let email = $('#appointment_check input[name="email"]').val();
            $.ajax({
                url: fetchAppointmentStatus.ajax_url,
                type: 'POST',
                data: {action:'imit_check_booking_status', email:email, nonce:fetchAppointmentStatus.imit_nonce},
                success: function(data){
                    $('#booking_status_spinner').fadeOut('fast');
                    $('#booking_status').html(data).fadeIn('fast');
                }
            });
        });


    });
})(jQuery)