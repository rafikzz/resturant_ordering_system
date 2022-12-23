<script>
    $(document).on('click', '.get-detail', function() {
        let order_id = $(this).attr('rel');
        $('#modal-lg').modal('toggle');
        $('.get-detail').attr('disabled', true);

        clearModal();

        $.ajax({
            type: 'GET',
            url: '{{ route('admin.orders.getOrderDetail') }}',
            data: {
                'order_id': order_id
            },
            beforeSend: function() {
                $('#overlay').show();
            },
            success: function(data) {
                if (data.status === 'success') {
                    setModalData(data.order, data.customer);
                    $('#get-bill').attr('href', data.billRoute);

                    data.orderItems.forEach(function(item) {
                        $('#table-items').append(template(item.item.name, item
                            .total_quantity, parseFloat(item.average_price)));
                    });
                    $('#table-items').append(
                        "<tr><td colspan='3'>Total</td><td>" +
                        foramtValue(data.order.total) + "</td></tr>");
                    if (data.order.discount && data.order.discount != 0 || data.order.status_id ==
                        3) {
                        $('#table-items').append(
                            "<tr><td colspan='3'>Discount</td><td>" +
                            foramtValue(data.order.discount) + "</td></tr>");
                    }

                    if (data.order.service_charge && data.order.service_charge != 0) {
                        $('#table-items').append(
                            "<tr><td colspan='3'>Service Charge</td><td>" +
                            foramtValue(data.order.service_charge) + "</td></tr>");
                    }
                    if (data.order.tax && data.order.tax != 0) {
                        $('#table-items').append(
                            "<tr><td colspan='3'>Tax</td><td>" +
                            foramtValue(data.order.tax) + "</td></tr>");
                    }
                    if (data.order.delivery_charge && data.order.delivery_charge != 0) {
                        $('#table-items').append(
                            "<tr><td colspan='3'>Packaging Charge</td><td>" +
                            foramtValue(data.order.delivery_charge) + "</td></tr>");
                    }
                    if (data.order.net_total) {
                        $('#table-items').append(
                            "<tr><td colspan='3'>Net Total</td><td>" +
                            foramtValue(data.order.net_total) + "</td></tr>");
                    }

                } else {
                    console.log('false');
                }
                $('#overlay').hide();
                $('.get-detail').attr('disabled', false);


            },
            error: function(xhr) {

                $('#overlay').hide();
                $('.get-detail').attr('disabled', false);
                console.log('Internal Serve Error');
            }
        });
    });

    function template(name, total_quantity, price) {

        return '<tr><td>' + name + '</td><td>' + total_quantity + '</td><td>Rs. ' +
            price + '</td><td>Rs. ' +
            price * total_quantity + '</td><</tr>';
    }

    function clearModal() {
        $('#bill-no').html('');
        $('#customer-name').html('');
        $('#customer-contact').html('');
        $('#order-date').html('');
        $('#order-status').html('');
        $('#menu-type').html('');
        $('#table-items').html('');
        $('#depart').html('');
        $('#note').html('');

        $('#department').css('display', 'none');
        $('#register-no').html('');
        $('#patient').css('display', 'none');
        $('#destination').html('');
        $('#get-bill').attr('href', 'javascript:void(0)');
    }

    function setModalData(order, customer) {
        $('#bill-no').html(order.bill_no);
        if (order.guest_menu == 1) {
            $('#menu-type').html('Guest Menu');
        } else {
            $('#menu-type').html('Staff Menu');
        }
        $('#customer-name').html(order.customer.name);
        $('#customer-type').html(customer.customer_type.name);
        $('#customer-contact').html(order.customer.phone_no);
        $('#order-date').html(order.order_datetime);
        $('#order-status').html(order.status.title);
        let destination="";
        let destination_no="";
        if (order.destination) {
            destination = order.destination;
        }
        if (order.destination_no) {
            destination_no = order.destination_no;
        }
        $('#destination').html(`${destination} ${destination_no}`);

        if (customer.staff) {
            if (customer.staff.department) {
                $('#department').css('display', 'block');
                $('#depart').html(customer.staff.department.name);
            }
        }
        if (customer.patient) {
            $('#patient').css('display', 'block');
            $('#register-no').html(customer.patient.register_no);
        }
        $('#note').html(order.note);
        if (order.is_credit) {
            $('#payment-type').html('Account');

        } else {
            $('#payment-type').html('Cash');
        }
    }

    function foramtValue(val) {
        return 'Rs. ' + val;
    }
</script>
