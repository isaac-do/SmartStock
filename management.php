<?php
$conn = new mysqli("localhost", "root", "", "smartstock");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

    // CREATE CUSTOMER 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_customer'])) {
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $customer_type = $_POST['customer_type'];
    $sales_rep_id = $_POST['sales_rep_id'];
    $billing_address = $_POST['billing_address'];
    $shipping_address = $_POST['shipping_address'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("INSERT INTO Customer (CustomerID, CompanyName, CustomerType, SalesRepID, BillingAddress, ShippingAddress, Email, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $customer_id, $customer_name, $customer_type, $sales_rep_id, $billing_address, $shipping_address, $email, $phone_number);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

// UPDATE CUSTOMER 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_customer'])) {
    $customer_id = $_POST['edit_customer_id'];
    $customer_name = $_POST['edit_cust_name'];
    $customer_type = $_POST['edit_cust_type'];
    $sales_rep_id = $_POST['edit_sales_rep_id'];
    $billing_address = $_POST['edit_bill_address'];
    $shipping_address = $_POST['edit_ship_address'];
    $email = $_POST['edit_cust_email'];
    $phone_number = $_POST['edit_phone_number'];

    $stmt = $conn->prepare("UPDATE Customer SET CompanyName=?, CustomerType=?, SalesRepID=?, BillingAddress=?, ShippingAddress=?, Email=?, PhoneNumber=? WHERE CustomerID=?");
    $stmt->bind_param("ssssssss", $customer_name, $customer_type, $sales_rep_id, $billing_address, $shipping_address, $email, $phone_number, $customer_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

// DELETE CUSTOMER 
if (isset($_GET['delete_customer'])) {
    $customer_id = $_GET['delete_customer'];

    $stmt = $conn->prepare("DELETE FROM Customer WHERE CustomerID = ?");
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

// CREATE LOCATION 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_location'])) {
    $location_id = $_POST['location_id'];
    $location_name = $_POST['location_name'];
    $location_type = $_POST['location_type'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO Locations (LocationID, LocationName, LocationType, Address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $location_id, $location_name, $location_type, $address);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

// UPDATE LOCATION 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_location'])) {
    $location_id = $_POST['edit_location_id'];
    $location_name = $_POST['edit_location_name'];
    $location_type = $_POST['edit_location_type'];
    $address = $_POST['edit_address'];

    $stmt = $conn->prepare("UPDATE Locations SET LocationName=?, LocationType=?, Address=? WHERE LocationID=?");
    $stmt->bind_param("ssis", $location_name, $location_type, $address, $location_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

// DELETE LOCATION 
if (isset($_GET['delete'])) {
    $location_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM Locations WHERE LocationID = ?");
    $stmt->bind_param("s", $location_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

    // CREATE Supplier
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_supplier'])) {
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $sales_rep_id = $_POST['supp_sales_rep_id'];
    $address = $_POST['supplier_address'];
    $email = $_POST['supplier_email'];
    $phone_number = $_POST['supplier_phone_number'];

    $stmt = $conn->prepare("INSERT INTO SupplierRecords (SupplierID, Name, SalesRepID, Address, Email, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $supplier_id, $supplier_name, $sales_rep_id, $address, $email, $phone_number);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

// UPDATE Supplier
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_supplier'])) {
    $supplier_id = $_POST['edit_supp_id'];
    $supplier_name = $_POST['edit_supp_name'];
    $sales_rep_id = $_POST['edit_supp_sales_rep_id'];
    $address = $_POST['edit_supp_address'];
    $email = $_POST['edit_supp_email'];
    $phone_number = $_POST['edit_supp_phone_number'];

    $stmt = $conn->prepare("UPDATE SupplierRecords SET Name=?, SalesRepID=?, Address=?, Email=?, PhoneNumber=? WHERE SupplierID=?");
    $stmt->bind_param("ssssss", $supplier_name, $sales_rep_id, $address, $email, $phone_number, $supplier_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

// DELETE Supplier
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Check if it's a SupplierID or a SalesRepID (optional logic split)
    $stmt = $conn->prepare("DELETE FROM SupplierRecords WHERE SupplierID = ?");
    $stmt->bind_param("s", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_sr'])) {
    $sales_id = $_POST['sales_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("INSERT INTO SalesRepresentative (SalesRepID, Name, Email, PhoneNumber) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $sales_id, $name, $email, $phone_number);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_sr'])) {
    $sales_id = $_POST['edit_sales_id'];
    $name = $_POST['edit_sales_name'];
    $email = $_POST['edit_sales_email'];
    $phone_number = $_POST['edit_sales_phone_number'];

    $stmt = $conn->prepare("UPDATE SalesRepresentative SET SalesRepID=?, Name=?, Email=?, PhoneNumber=? WHERE SalesRepID=?");
    $stmt->bind_param("sssis", $sales_id, $name, $email, $phone_number, $sales_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}

if (isset($_GET['delete'])) {
    $sales_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM SalesRepresentative WHERE SalesRepID = ?");
    $stmt->bind_param("s", $sales_id);
    $stmt->execute();
    $stmt->close();

    header("Location: management.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock ERP</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="topbar">
        <div class="titletext"><strong>SmartStock ERP</strong></div>
        <nav class="topnav">
            <a href="index.php">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="purchaseorder.php">Purchase Orders</a>
            <a href="transferorder.php">Transfer Orders</a>
            <a href="management.php">Management</a>
        </nav>
    </header>

    <!-- CUSTOMER MANAGEMENT SECTION -->
    <div class="dashboard">
        <h1>Customer Management</h2>
            <div class="actions">
                <button class="btn" onclick="showCustomerCreateForm()">Create Customer Record</button>
                <input type="text" id="searchInput" placeholder="Search Customer ID" />
                <button class="btn" onclick="">Search</button>
            </div>

            <!-- This is the customer record create form-->
            <div id="createCustomerForm" class="toggle-form">
                <h2>Create New Customer Record</h2>
                <label for="customer_id">Customer ID</label>
                <input type="text" id="customer_id" name="customer_id" />

                <label for="customer_name">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" />

                <label for="customer_type">Customer Type</label>
                <input type="text" id="customer_type" name="customer_type" />

                <label for="sales_rep_id">Sales Rep ID</label>
                <input type="text" id="sales_rep_id" name="sales_rep_id" />

                <label for="billing_address">Billing Address</label>
                <input type="text" id="billing_address" name="billing_address" />

                <label for="ship_address">Shipping Address</label>
                <input type="text" id="ship_address" name="ship_address" />

                <label for="email">Email</label>
                <input type="text" id="email" name="email" />

                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" />

                <button class="btn" onclick="alert('Customer Record Created.')">Create</button>
            </div>
            <!-- This is the customer record edit form-->
            <div id="editCustomerForm" class="modal-overlay">
                <div class="modal-content">
                    <h2>Edit Customer Record</h2>
                    <div class="form-group">
                        <label>Customer ID</label>
                        <input type="text" id="edit_customer_id" name="edit_customer_id" disabled />
                    </div>
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" id="edit_cust_name" name="edit_cust_name" />
                    </div>
                    <div class="form-group">
                        <label>Customer Type</label>
                        <input type="text" id="edit_cust_type" name="edit_cust_type" />
                    </div>
                    <div class="form-group">
                        <label>Sales Rep ID</label>
                        <input type="text" id="edit_sales_rep_id" name="edit_sales_rep_id" />
                    </div>
                    <div class="form-group">
                        <label>Billing Address</label>
                        <input type="text" id="edit_bill_address" name="edit_bill_address" />
                    </div>
                    <div class="form-group">
                        <label>Shipping Address</label>
                        <input type="text" id="edit_ship_address" name="edit_ship_address" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" id="edit_cust_email" name="edit_cust_email" />
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="edit_phone_number" name="edit_phone_number" />
                    </div>
                    <button class="btn" onclick="alert('Changes saved.')">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeCustomerEditForm()">Cancel</button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Company Name</th>
                        <th>Customer Type</th>
                        <th>Sales Rep ID</th>
                        <th>Billing Address</th>
                        <th>Shipping Address</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $customer_search = isset($_GET['customer_search']) ? $_GET['customer_search'] : '';
                    if (!empty($customer_search)) {
                        $stmt = $conn->prepare("SELECT * FROM customer WHERE CustomerID LIKE ?");
                        $likeSearch = "%$customer_search%";
                        $stmt->bind_param("s", $likeSearch);
                        $stmt->execute();
                        $cust_result = $stmt->get_result();
                    } 
                    else
                        $cust_result = $conn->query("SELECT * FROM customer");

                    while ($row = $cust_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['CustomerID']) ?></td>
                            <td><?= htmlspecialchars($row['CompanyName']) ?></td>
                            <td><?= htmlspecialchars($row['CustomerType']) ?></td>
                            <td><?= htmlspecialchars($row['SalesRepID']) ?></td>
                            <td><?= htmlspecialchars($row['BillingAddress']) ?></td>
                            <td><?= htmlspecialchars($row['ShippingAddress']) ?></td>
                            <td><?= htmlspecialchars($row['Email']) ?></td>
                            <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                            <td class="actions-row">
                                <button class="btn">Edit</button>
                                <button class="btn btn-danger">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </section>
    </div>

    <!-- SUPPLIER MANAGEMENT -->
    <div class="dashboard">
        <h1>Supplier Records</h1>
        <div class="actions">
            <button class="btn" onclick="showSupplierCreateForm()">Create Supplier Record</button>
            <form method="GET">
                <input type="text" id="searchInput" placeholder="Search Supplier ID" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <button class="btn" type="Submit">Search</button>
            </form>
        </div>

        <!-- This is the supplier record create form-->
        <div id="createSupplierForm" class="toggle-form">
            <form method="POST">
                <h2>Create New Supplier Record</h2>
                <label for="supplier_id">Supplier ID</label>
                <input type="text" id="supplier_id" name="supplier_id" required />

                <label for="supplier_name">Supplier Name</label>
                <input type="text" id="supplier_name" name="supplier_name" required/>

                <label for="supp_sales_rep_id">Sales Rep ID</label>
                <input type="text" id="supp_sales_rep_id" name="supp_sales_rep_id" required/>

                <label for="supplier_address">Address</label>
                <input type="text" id="supplier_address" name="supplier_address" required/>

                <label for="supplier_email">Email</label>
                <input type="text" id="supplier_email" name="supplier_email" required/>

                <label for="supplier_phone_number">Phone Number</label>
                <input type="text" id="supplier_phone_number" name="supplier_phone_number" required/>

                <button class="btn" type="submit" name="create_supplier" onclick="alert('Supplier Record Created.')">Create</button>
            </form>
        </div>
        <!-- This is the supplier record edit form-->
        <div id="editSupplierForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Supplier Record Form</h2>
                    <div class="form-group">
                        <label>Supplier ID</label>
                        <input type="text" id="edit_supp_id" name="edit_supp_id" readonly />
                    </div>
                    <div class="form-group">
                        <label>Supplier Name</label>
                        <input type="text" id="edit_supp_name" name="edit_supp_name" />
                    </div>
                    <div class="form-group">
                        <label>Sales Rep ID</label>
                        <input type="text" id="edit_supp_sales_rep_id" name="edit_supp_sales_rep_id" />
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" id="edit_supp_address" name="edit_supp_address" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" id="edit_supp_email" name="edit_supp_email" />
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="edit_supp_phone_number" name="edit_supp_phone_number" />
                    </div>
                    <button class="btn" type="submit" name="update_supplier" onclick="alert('Changes saved.')">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeSupplierEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Sales Rep ID</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <!-- Temporary data. Will need to implement PHP to pull from database. -->
            <tbody id ="toTable">
                <?php 
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if (!empty($search)) {
                    $stmt = $conn->prepare("SELECT * FROM supplier WHERE SupplierID LIKE ?");
                    $likeSearch = "%$search%";
                    $stmt->bind_param("s", $likeSearch);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } 
                else
                    $result = $conn->query("SELECT * FROM supplier");

                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["SupplierID"]) ?></td>
                        <td><?= htmlspecialchars($row["Name"]) ?></td>
                        <td><?= htmlspecialchars($row["SalesRepID"]) ?></td>
                        <td><?= htmlspecialchars($row["Address"]) ?></td>
                        <td><?= htmlspecialchars($row["Email"]) ?></td>
                        <td><?= htmlspecialchars($row["PhoneNumber"]) ?></td>
                        <td class="actions-row">
                            <button class="btn"
                                onclick="showSupplierEditForm(
                                '<?= htmlspecialchars($row['SupplierID']) ?>',
                                '<?= htmlspecialchars($row['Name']) ?>',
                                '<?= htmlspecialchars($row['SalesRepID']) ?>',
                                '<?= htmlspecialchars($row['Address']) ?>',
                                '<?= htmlspecialchars($row['Email']) ?>',
                                '<?= htmlspecialchars($row['PhoneNumber']) ?>'
                                )">Edit</button>
                            <a class="btn btn-danger" href="management.php?delete=<?= urlencode($row['SupplierID']) ?>"
                                onclick="return confirm('Are you sure you want to delete this Supplier?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- LOCATION MANAGEMENT -->
    <div class="dashboard">
        <h1>Locations</h2>
            <div class="actions">
                <button class="btn" onclick="showLocationCreateForm()">Create Location Record</button>
                <input type="text" id="searchInput" placeholder="Search Location" />
                <button class="btn" onclick="">Search</button>
            </div>

            <!-- This is the location record create form-->
            <div id="createLocationForm" class="toggle-form">
                <h2>Create New Location Record</h2>
                <label for="address">Address</label>
                <input type="text" id="address" name="address" />

                <label for="location_type">Location Type</label>
                <input type="text" id="location_type" name="location_type" />

                <label for="location_name">Location Name</label>
                <input type="text" id="location_name" name="location_name" />

                <label for="location_id">Location ID</label>
                <input type="text" id="location_id" name="location_id" />
                <button class="btn" onclick="alert('Location Record Created.')">Create</button>
            </div>
            <!-- This is the location record edit form-->
            <div id="editLocationForm" class="modal-overlay">
                <div class="modal-content">
                    <h2>Edit Location Record</h2>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" id="address" name="address" disabled />
                    </div>
                    <div class="form-group">
                        <label>Location Type</label>
                        <input type="text" id="location_type" name="location_type" />
                    </div>
                    <div class="form-group">
                        <label>Location Name</label>
                        <input type="text" id="location_name" name="location_name" />
                    </div>
                    <div class="form-group">
                        <label>Location ID</label>
                        <input type="text" id="location_id" name="location_id" />
                    </div>
                    <button class="btn" onclick="alert('Changes saved.')">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeLocationEditForm()">Cancel</button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Address</th>
                        <th>Location Type</th>
                        <th>Location Name</th>
                        <th>Location ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="locationTable">
                    <?php
                    $location_search = isset($_GET['location_search']) ? $_GET['location_search'] : '';
                    if (!empty($location_search)) {
                        $stmt = $conn->prepare("SELECT * FROM location WHERE LocationID LIKE ?");
                        $likeSearch = "%$location_search%";
                        $stmt->bind_param("s", $likeSearch);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else {
                        $result = $conn->query("SELECT * FROM location");
                    }
                
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["Address"]) ?></td>
                            <td><?= htmlspecialchars($row["LocationType"]) ?></td>
                            <td><?= htmlspecialchars($row["LocationName"]) ?></td>
                            <td><?= htmlspecialchars($row["LocationID"]) ?></td>
                            <td class="actions-row">
                                <button class="btn"
                                    onclick="showLocationEditForm(
                                        '<?= htmlspecialchars($row['Address']) ?>',
                                        '<?= htmlspecialchars($row['LocationType']) ?>',
                                        '<?= htmlspecialchars($row['LocationName']) ?>',
                                        '<?= htmlspecialchars($row['LocationID']) ?>'
                                    )">Edit</button>
                                <a class="btn btn-danger" href="management.php?delete=<?= urlencode($row['LocationID']) ?>"
                                   onclick="return confirm('Are you sure you want to delete this Location?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
    </div>



    <!-- SALES REPRESENTATIVE -->
    <div class="dashboard">
        <h1>Sales Representative</h1>
        <div class="actions">
            <button class="btn" onclick="showSalesCreateForm()">Create Sales Rep Record</button>
            <form method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search Sales Representative ID" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <button class="btn" type="Submit">Search</button>
            </form>
        </div>

        <!-- This is the sales record create form-->
        <div id="createSalesForm" class="toggle-form">
            <form method="POST">
                <h2>Create New Sales Record</h2>
                <label for="sales_id">Sales Rep ID</label>
                <input type="text" id="sales_id" name="sales_id" required/>

                <label for="name">Name</label>
                <input type="text" id="name" name="name" required/>

                <label for="email">Email</label>
                <input type="text" id="email" name="email" required/>

                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" required/>
                
                <button class="btn" type="submit" name="create_sr" onclick="alert('Sales Rep Created.')">Create</button>
            </form>
        </div>
        <!-- This is the sales record edit form-->
        <div id="editSalesForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Sales Form</h2>
                    <div class="form-group">
                        <label>Sales ID</label>
                        <input type="text" id="edit_sales_id" name="edit_sales_id" readonly/>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="edit_sales_name" name="edit_sales_name" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" id="edit_sales_email" name="edit_sales_email" />
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="edit_sales_phone_number" name="edit_sales_phone_number" />
                    </div>
                    <button class="btn" type="submit" name="update_sr" onclick="alert('Changes saved.')">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeSalesEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Sales Rep ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <!--Temporary data. Will need to implement PHP to pull from database.-->
            <tbody id="toTable">
                <?php
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if (!empty($search)) {
                    $stmt = $conn->prepare("SELECT * FROM SalesRepresentative WHERE SalesRepID LIKE ?");
                    $likeSearch = "%$search%";
                    $stmt->bind_param("s", $likeSearch);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } 
                else
                    $result = $conn->query("SELECT * FROM SalesRepresentative");

                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["SalesRepID"]) ?></td>
                        <td><?= htmlspecialchars($row["Name"]) ?></td>
                        <td><?= htmlspecialchars($row["Email"]) ?></td>
                        <td><?= htmlspecialchars($row["PhoneNumber"]) ?></td>
                        <td class="actions-row">
                            <button class="btn"
                                onclick="showSalesEditForm(
                                '<?= htmlspecialchars($row['SalesRepID']) ?>',
                                '<?= htmlspecialchars($row['Name']) ?>',
                                '<?= htmlspecialchars($row['Email']) ?>',
                                '<?= htmlspecialchars($row['PhoneNumber']) ?>'
                                )">Edit</button>
                            <a class="btn btn-danger" href="management.php?delete=<?= urlencode($row['SalesRepID']) ?>"
                                onclick="return confirm('Are you sure you want to delete this Purchase Order?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>


    <script>
        function showCustomerCreateForm() {
            const form = document.getElementById('createCustomerForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
            document.getElementById('editForm').style.display = 'none';
        }

        function showSupplierCreateForm() {
            const form = document.getElementById('createSupplierForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        function showLocationCreateForm() {
            const form = document.getElementById('createLocationForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
            document.getElementById('editForm').style.display = 'none';
        }

        function showSalesCreateForm() {
            const form = document.getElementById('createSalesForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        function showSalesEditForm(sales_rep_id, name, email, phone_number) {
            document.getElementById('edit_sales_id').value = sales_rep_id;
            document.getElementById('edit_sales_name').value = name;
            document.getElementById('edit_sales_email').value = email;
            document.getElementById('edit_sales_phone_number').value = phone_number;
            document.getElementById('editSalesForm').style.display = 'flex';
        }

        function closeSalesEditForm() {
            document.getElementById('editSalesForm').style.display = 'none';
        }

        function closeSalesEditForm() {
            document.getElementById('editSalesForm').style.display = 'none';
        }

        function showCustomerEditForm(customer_id, customer_name, customer_type, sales_rep_id, billing_address, ship_address, email, phone_number) {
            document.getElementById('edit_customer_id').value = customer_id;
            document.getElementById('edit_cust_name').value = customer_name;
            document.getElementById('edit_cust_type').value = customer_type;
            document.getElementById('edit_sales_rep_id').value = sales_rep_id;
            document.getElementById('edit_bill_address').value = billing_address;
            document.getElementById('edit_ship_address').value = ship_address;
            document.getElementById('edit_cust_email').value = email;
            document.getElementById('edit_phone_number').value = phone_number;

            document.getElementById('editCustomerForm').style.display = 'flex';
        }

        function showSupplierEditForm(supplier_id, supplier_name, supp_sales_rep_id, supplier_address, supplier_email, supplier_phone_number) {
            document.getElementById('edit_supp_id').value = supplier_id;
            document.getElementById('edit_supp_name').value = supplier_name;
            document.getElementById('edit_supp_sales_rep_id').value = supp_sales_rep_id;
            document.getElementById('edit_supp_address').value = supplier_address;
            document.getElementById('edit_supp_email').value = supplier_email;
            document.getElementById('edit_supp_phone_number').value = supplier_phone_number;

            document.getElementById('editSupplierForm').style.display = 'flex';
        }

        function showLocationEditForm(address, location_type, location_name, location_id) {
            document.getElementById('edit_location_id').value = location_id;
            document.getElementById('edit_location_name').value = location_name;
            document.getElementById('edit_location_address').value = address;
            document.getElementById('edit_location_type').value = location_type;

            document.getElementById('editLocationForm').style.display = 'flex';
        }


        function closeCustomerEditForm() {
            document.getElementById('editCustomerForm').style.display = 'none';
        }

        function closeSupplierEditForm() {
            document.getElementById('editSupplierForm').style.display = 'none';
        }

        function closeLocationEditForm() {
            document.getElementById('editLocationForm').style.display = 'none';
        }
    </script>
    </div>
</body>

</html>