<script>
    //Getting Customer Type
    $('#customer_type').on('change', function() {
        let customer_type = parseInt($(this).val());
        $("#payment_type option[value='1']").attr("disabled", false);

        if (customer_type === 2) {
            $("#payment_type").val('0').trigger('change');
        } else {
            $("#payment_type").val(0).trigger('change');
            $("#payment_type option[value='1']").attr("disabled", "disabled");
        }
        $.ajax({
            type: 'GET',
            url: '{{ route('admin.customer.getType') }}',
            data: {
                'customer_type': customer_type,
            },
            success: function(data) {

                if (data.status === 'success') {
                    $('#oldCustomer').find('option').not(':first').remove();
                    data.customers.forEach(function(customer) {
                        let register_no = '';
                        let code = '';
                        let department = '';
                        let text = '';


                        if (customer.patient) {
                            register_no = " Register No:" + customer.patient.register_no;
                            text = code + ' ' + customer.name + '(' + customer.phone_no +
                                ')' + register_no;
                        }
                        if (customer.staff) {
                            code = customer.staff.code;
                            if (customer.staff) {
                                code = customer.staff.code;
                                if (customer.staff.department) {
                                    department = ' Department: ' + customer.staff.department
                                        .name;

                                }
                                text = code + ' ' + customer.name + '(' + customer
                                    .phone_no +
                                    ') ' + department;
                            }


                        }

                        let newOption = new Option(text, customer.id, true,
                            true);
                        $('#oldCustomer').append(newOption);
                    });
                    if (customer_type == 1) {
                        $('#oldCustomer').val(1);

                    } else {
                        $('#oldCustomer').val(null);
                    }
                    $("input[name=new_or_old]:checked").trigger('click');


                } else {
                    console.log(data.message);

                }
            },
            error: function(xhr) {
                console.log('Internal Sever Error');

            }
        });

    });
    //For Inputing Existing Customer
    $('#existingCustomer').click(function() {
            if ($(this).is(':checked')) {
                $('#old').css('display', 'block');
                $('#new').css('display', 'none');
                $('#old :input').prop('disabled', false);
                $('#new :input').prop('disabled', true);
            }
        }


    );

    //For Inputing New Customer
    $('#newCustomer').click(function() {
            if ($(this).is(':checked')) {
                $('#old').css('display', 'none');
                $('#new').css('display', 'block');
                $('#old :input').prop('disabled', true);
                $('#new :input').prop('disabled', false);
            }
            if ($('#customer_type').val() == 3) {
                $('#patient-reg').css('display', 'block');
                $('#patient_register_no').prop('disabled', false);
                $('.staff-block').css('display', 'none');
                $('#code').prop('disabled', true);
                $('#department_id').prop('disabled', true);
            } else if ($('#customer_type').val() == 2) {
                $('#patient-reg').css('display', 'none');
                $('#patient_register_no').prop('disabled', true);
                $('.staff-block').css('display', 'block');
                $('#code').prop('disabled', false);
                $('#department_id').prop('disabled', false);
            } else {
                $('#patient-reg').css('display', 'none');
                $('#patient_register_no').prop('disabled', true);
                $('.staff-block').css('display', 'none');
                $('#code').prop('disabled', true);
                $('#department_id').prop('disabled', true);
            }
        }


    );
</script>
