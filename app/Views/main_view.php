<?=$this->extend("master_view")?>

<?=$this->section("content")?>
    <div class="dashboard">
        <h1>Orders</h1>
        <div class="dashboard__orderTable">
            <div class="dashboard__orderTable--search">
                <input type="text" class="form-control" placeholder="Search order here">
                <select name="" id="" class="form-control">
                    <option value="">Show All</option>
                    <option value="">Shipped</option>
                    <option value="">Cancelled</option>
                </select>
            </div>
            <div class="dashboard__orderTable--tableContainer">
                <table class="tbl">
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
                        <tr>
                            <td>199</td>
                            <td>Melkisedek Ubalde</td>
                            <td>9/6/2014</td>
                            <td>Salvacion, Virac, Catanduanes 4800</td>
                            <td>$49.99</td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Status</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>199</td>
                            <td>Melkisedek Ubalde</td>
                            <td>9/6/2014</td>
                            <td>Salvacion, Virac, Catanduanes 4800</td>
                            <td>$49.99</td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Status</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>199</td>
                            <td>Melkisedek Ubalde</td>
                            <td>9/6/2014</td>
                            <td>Salvacion, Virac, Catanduanes 4800</td>
                            <td>$49.99</td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Status</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>199</td>
                            <td>Melkisedek Ubalde</td>
                            <td>9/6/2014</td>
                            <td>Salvacion, Virac, Catanduanes 4800</td>
                            <td>$49.99</td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Status</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>199</td>
                            <td>Melkisedek Ubalde</td>
                            <td>9/6/2014</td>
                            <td>Salvacion, Virac, Catanduanes 4800</td>
                            <td>$49.99</td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Status</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>199</td>
                            <td>Melkisedek Ubalde</td>
                            <td>9/6/2014</td>
                            <td>Salvacion, Virac, Catanduanes 4800</td>
                            <td>$49.99</td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Status</option>
                                </select>
                            </td>
                        </tr>

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
        $(document).ready(function() {
        });
    </script>
<?=$this->endSection()?>