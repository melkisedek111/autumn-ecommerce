<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="dashboard">
        <h1>Orders</h1>
        <div class="dashboard__orderTable">
            <div class="dashboard__orderTable--search">
                <input type="text" class="form-control" id="searchOrder" placeholder="Search order here">
                <select name="" id="filterOrders" class="form-control">
                    <option value="All">Show All</option>
                    <option value="Order in process">Order in process</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="dashboard__orderTable--tableContainer">
                <table class="tbl" id="orderTable">
                    <thead>
                        <tr>
                            <th width="10%">Order ID</th>
                            <th width="20%">Name</th>
                            <th width="10%">Date</th>
                            <th width="40%">Billing Address</th>
                            <th width="5%">Total</th>
                            <th width="15%">Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div class="tablePagination">
                    <a href="">First Page</a>
                    <a href="">Previous Page</a>
                    <a href="">1</a>
                    <a href="">2</a>
                    <a href="">3</a>
                    <a href="">4</a>
                    <a href="">Next Page</a>
                    <a href="">Last Page</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {
            const orders = <?= $orders->JSON_ORDER; ?>;
            // const fakeData = await fetch('http://www.json-generator.com/api/json/get/cgBCWKKBiW?indent=1');
            // const orders = await fakeData.json();
            console.log(orders);

            
            $(document).on('click', '#paginate', function(e){
                    e.preventDefault();
                    const pageLinkDisabled = $(this).attr('disabled');
                    if(!pageLinkDisabled) {
                        const pageNumber = $(this).attr('page-number') ? $(this).attr('page-number') : 1;
                        let to = pageNumber * 10;
                        let from = to - 10;
                        setPagination(orders.length, parseInt(pageNumber), 10);
                        setTable(orders,from, to);
                    }
                });

            $(document).on('keyup', '#searchOrder', function() {
                var value = $(this).val().toLowerCase();
                $("#orderTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            })
            
            function setTable(data, from, to) {
                let orderTable = '';
                const newData = data.filter((data, idx) => from < idx + 1 && to >= idx + 1);
                for(const order of newData) {
                    orderTable += `
                        <tr>
                            <td><a href="/orders/${order.id}">${order.id}</a></td>
                            <td>${order.first_name} ${order.last_name}</td>
                            <td>${order.created_at}</td>
                            <td>${order.address}, ${order.city}, ${order.province}, ${order.zipcode}</td>
                            <td>$${order.total_amount}</td>
                            <td>
                                <select name="" id="" class="form-control updateOrderStatus" data-id="${order.id}">          
                                        <option value="Order in process" ${order.order_status == 'Order in process' ? 'selected' : ''} >Order in process</option>
                                        <option value="Shipped" ${order.order_status == 'Shipped' ? 'selected' : ''}>Shipped</option>
                                        <option value="Cancelled" ${order.order_status == 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </td>
                        </tr>
                    `;
                }
                $('tbody').html(orderTable);
            }

            $(document).on('change', '#filterOrders', function(e) {
                const filterValue = $(this).val();
                $("#orderTable tr").filter(function() {
                    if(filterValue == 'All') {
                        $(this).show();
                    } else {
                        $(this).toggle($(this).find('td').find('select').val() == filterValue);
                    }
                });
            });

            $(document).on('change', '.updateOrderStatus', function(e) {
                const orderId = $(this).attr('data-id');
                const setValue = $(this).val();
                const data = {
                    order_id: orderId,
                    set_value: setValue
                };
                const response = ajax(data, '/admin/update_order_status');
                response.done(e => {
                    refreshToken(e);
                    if(e.internalValidationError) {
                        alertMessage(e.internalValidationErrorMessage, 'alertDanger'); // --> message if there are somethings wrong in validations
                    } else {
                        if(e.orderStatusUpdated) {
                            alertMessage(e.orderStatusUpdated, 'alertSuccess'); // --> message if there are somethings wrong in validations
                        }
                    }
                })
            });

            setPagination(orders.length, 1, 10);
            setTable(orders, 0, 10);
        });
    </script>
<?=$this->endSection()?>