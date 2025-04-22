<?php
$conn = new mysqli("localhost", "root", "", "smartstock");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if tables exists
function table_exists($conn, $table_name)
{
    $table_name_escaped = $conn->real_escape_string($table_name);
    $result = $conn->query("SHOW TABLES LIKE '$table_name_escaped'");
    return $result && $result->num_rows > 0;
}

// Check if the table has the entity
function exists_in_table($conn, $table, $column, $value) {
    $stmt = $conn->prepare("SELECT 1 FROM $table WHERE $column = ? LIMIT 1");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// CREATE CUSTOMER 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['create_customer'])) {
        try {
            $customer_id = $_POST['customer_id'];
            $customer_name = $_POST['customer_name'];
            $customer_type = $_POST['customer_type'];
            $sales_rep_id = $_POST['cust_sales_rep_id'];
            $billing_address = $_POST['cust_billing_address'];
            $shipping_address = $_POST['cust_shipping_address'];
            $email = $_POST['cust_email'];
            $phone_number = $_POST['cust_phone_number'];

            // Foreign key checks
            if (!exists_in_table($conn, "SalesRepresentative", "SalesRepID", $sales_rep_id)) {
                header("Location: error.php?code=fk_customer_salesrep");
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO Customer (CustomerID, CompanyName, CustomerType, SalesRepID, BillingAddress, ShippingAddress, Email, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $customer_id, $customer_name, $customer_type, $sales_rep_id, $billing_address, $shipping_address, $email, $phone_number);

            if ($stmt->execute())
                $successMessage = "Customer Record created successfully!";
            else
                $errorMessage = "Error: " . $stmt->error;

            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            header("Location: error.php?code=unknown&msg=" . urlencode($e->getMessage()));
            exit;
        }
    }

    // UPDATE CUSTOMER 
    if (isset($_POST['update_customer'])) {
        $customer_id = $_POST['edit_customer_id'];
        $customer_name = $_POST['edit_cust_name'];
        $customer_type = $_POST['edit_cust_type'];
        $sales_rep_id = $_POST['edit_cust_sales_rep_id'];
        $billing_address = $_POST['edit_cust_bill_address'];
        $shipping_address = $_POST['edit_cust_ship_address'];
        $email = $_POST['edit_cust_email'];
        $phone_number = $_POST['edit_cust_phone_number'];

        $stmt = $conn->prepare("UPDATE Customer SET CompanyName=?, CustomerType=?, SalesRepID=?, BillingAddress=?, ShippingAddress=?, Email=?, PhoneNumber=? WHERE CustomerID=?");
        $stmt->bind_param("ssssssss", $customer_name, $customer_type, $sales_rep_id, $billing_address, $shipping_address, $email, $phone_number, $customer_id);

        if ($stmt->execute())
            $successMessage = "Customer Record updated successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    // DELETE CUSTOMER 
    if (isset($_POST['delete_cust'])) {
        $customer_id = $_POST['delete_cust_id'];

        $stmt = $conn->prepare("DELETE FROM Customer WHERE CustomerID = ?");
        $stmt->bind_param("s", $customer_id);

        if ($stmt->execute())
            $successMessage = "Customer Record deleted successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    // CREATE LOCATION 
    if (isset($_POST['create_location'])) {
        $location_id = $_POST['location_id'];
        $location_name = $_POST['location_name'];
        $location_type = $_POST['location_type'];
        $location_address = $_POST['location_address'];

        $stmt = $conn->prepare("INSERT INTO locations (LocationID, LocationName, LocationType, Address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $location_id, $location_name, $location_type, $location_address);

        if ($stmt->execute())
            $successMessage = "Location Record created successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    // UPDATE LOCATION 
    if (isset($_POST['update_location'])) {
        $location_id = $_POST['edit_location_id'];
        $location_name = $_POST['edit_location_name'];
        $location_type = $_POST['edit_location_type'];
        $location_address = $_POST['edit_location_address'];

        $stmt = $conn->prepare("UPDATE locations SET LocationName=?, LocationType=?, Address=? WHERE LocationID=?");
        $stmt->bind_param("ssss", $location_name, $location_type, $location_address, $location_id);

        if ($stmt->execute())
            $successMessage = "Location Record updated successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    // DELETE LOCATION 
    if (isset($_POST['delete_loc'])) {
        $location_id = $_POST['delete_loc_id'];

        $stmt = $conn->prepare("DELETE FROM locations WHERE LocationID = ?");
        $stmt->bind_param("s", $location_id);

        if ($stmt->execute())
            $successMessage = "Location Record deleted successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    // CREATE Supplier
    if (isset($_POST['create_supplier'])) {
        try {
            $supplier_id = $_POST['supplier_id'];
            $supplier_name = $_POST['supplier_name'];
            $sales_rep_id = $_POST['supp_sales_rep_id'];
            $address = $_POST['supplier_address'];
            $email = $_POST['supplier_email'];
            $phone_number = $_POST['supplier_phone_number'];

            $stmt = $conn->prepare("INSERT INTO supplier (SupplierID, SupplierName, SalesRepID, Address, Email, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $supplier_id, $supplier_name, $sales_rep_id, $address, $email, $phone_number);

            // Foreign key checks
            if (!exists_in_table($conn, "SalesRepresentative", "SalesRepID", $sales_rep_id)) {
                header("Location: error.php?code=fk_supplier_salesrep");
                exit;
            }

            if ($stmt->execute())
                $successMessage = "Supplier Record created successfully!";
            else
                $errorMessage = "Error: " . $stmt->error;

            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            header("Location: error.php?code=unknown&msg=" . urlencode($e->getMessage()));
            exit;
        }
    }

    // UPDATE Supplier
    if (isset($_POST['update_supplier'])) {
        $supplier_id = $_POST['edit_supp_id'];
        $supplier_name = $_POST['edit_supp_name'];
        $sales_rep_id = $_POST['edit_supp_sales_rep_id'];
        $address = $_POST['edit_supp_address'];
        $email = $_POST['edit_supp_email'];
        $phone_number = $_POST['edit_supp_phone_number'];

        $stmt = $conn->prepare("UPDATE supplier SET SupplierName=?, SalesRepID=?, Address=?, Email=?, PhoneNumber=? WHERE SupplierID=?");
        $stmt->bind_param("ssssss", $supplier_name, $sales_rep_id, $address, $email, $phone_number, $supplier_id);

        if ($stmt->execute())
            $successMessage = "Supplier Record updated successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    // DELETE Supplier
    if (isset($_POST['delete_sup'])) {
        $delete_id = $_POST['delete_sup_id'];

        // Check if it's a SupplierID or a SalesRepID (optional logic split)
        $stmt = $conn->prepare("DELETE FROM supplier WHERE SupplierID = ?");
        $stmt->bind_param("s", $delete_id);

        if ($stmt->execute())
            $successMessage = "Supplier Record deleted successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    if (isset($_POST['create_sr'])) {
        $sales_id = $_POST['sales_id'];
        $name = $_POST['sales_name'];
        $email = $_POST['sales_email'];
        $phone_number = $_POST['sales_phone_number'];

        $stmt = $conn->prepare("INSERT INTO SalesRepresentative (SalesRepID, Name, Email, PhoneNumber) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $sales_id, $name, $email, $phone_number);

        if ($stmt->execute())
            $successMessage = "Sales Rep Record created successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    if (isset($_POST['update_sr'])) {
        $sales_id = $_POST['edit_sales_id'];
        $name = $_POST['edit_sales_name'];
        $email = $_POST['edit_sales_email'];
        $phone_number = $_POST['edit_sales_phone_number'];

        $stmt = $conn->prepare("UPDATE SalesRepresentative SET SalesRepID=?, Name=?, Email=?, PhoneNumber=? WHERE SalesRepID=?");
        $stmt->bind_param("sssss", $sales_id, $name, $email, $phone_number, $sales_id);

        if ($stmt->execute())
            $successMessage = "Sales Rep Record updated successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    if (isset($_POST['delete_sales'])) {
        $sales_id = $_POST['delete_sales_id'];

        $stmt = $conn->prepare("DELETE FROM SalesRepresentative WHERE SalesRepID = ?");
        $stmt->bind_param("s", $sales_id);

        if ($stmt->execute())
            $successMessage = "Sales Rep Record deleted successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }
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

            <?php if (isset($successMessage)): ?>
                <div class="notification success"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <?php if (isset($errorMessage)): ?>
                <div class="notification error"><?php echo $errorMessage; ?></div>
            <?php endif; ?>

            <div class="actions">
                <button class="btn" onclick="showCustomerCreateForm()">Create Customer Record</button>
                <form method="GET">
                    <input type="text" id="searchInput" name="customer_search" placeholder="Search Customer ID" value="<?= isset($_GET['customer_search']) ? htmlspecialchars($_GET['customer_search']) : '' ?>" />
                    <button class="btn" type="submit">Search</button>
                </form>
            </div>

            <!-- This is the customer record create form-->
            <div id="createCustomerForm" class="toggle-form">
                <form method="POST">
                    <h2>Create New Customer Record</h2>
                    <label for="customer_id">Customer ID</label>
                    <input type="text" id="customer_id" name="customer_id" required />

                    <label for="customer_name">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" required />

                    <label for="customer_type">Customer Type</label>
                    <input type="text" id="customer_type" name="customer_type" required />

                    <label for="cust_sales_rep_id">Sales Rep ID</label>
                    <input type="text" id="cust_sales_rep_id" name="cust_sales_rep_id" required />

                    <label for="cust_billing_address">Billing Address</label>
                    <input type="text" id="cust_billing_address" name="cust_billing_address" />

                    <label for="cust_shipping_address">Shipping Address</label>
                    <input type="text" id="cust_shipping_address" name="cust_shipping_address" />

                    <label for="cust_email">Email</label>
                    <input type="text" id="cust_email" name="cust_email" />

                    <label for="cust_phone_number">Phone Number</label>
                    <input type="text" id="cust_phone_number" name="cust_phone_number" />

                    <button class="btn" type="submit" name="create_customer">Create</button>
                </form>
            </div>
            <!-- This is the customer record edit form-->
            <div id="editCustomerForm" class="modal-overlay">
                <form method="POST">
                    <div class="modal-content">
                        <h2>Edit Customer Record</h2>
                        <div class="form-group">
                            <label>Customer ID [readonly]</label>
                            <input type="text" id="edit_customer_id" name="edit_customer_id" readonly />
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
                            <input type="text" id="edit_cust_sales_rep_id" name="edit_cust_sales_rep_id" />
                        </div>
                        <div class="form-group">
                            <label>Billing Address</label>
                            <input type="text" id="edit_cust_bill_address" name="edit_cust_bill_address" />
                        </div>
                        <div class="form-group">
                            <label>Shipping Address</label>
                            <input type="text" id="edit_cust_ship_address" name="edit_cust_ship_address" />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" id="edit_cust_email" name="edit_cust_email" />
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="edit_cust_phone_number" name="edit_cust_phone_number" />
                        </div>
                        <button class="btn" type="submit" name="update_customer">Save Changes</button>
                        <button class="btn btn-secondary" onclick="closeCustomerEditForm()">Cancel</button>
                    </div>
                </form>
            </div>
            <!-- Confirmation Dialog -->
            <div id="confirmDialog" class="confirm-dialog">
                <div class="confirm-content">
                    <h3>Confirm Delete</h3>
                    <p>Are you sure you want to delete this Customer Record? This action cannot be undone.</p>
                    <form method="POST" action="" id="deleteForm">
                        <input type="hidden" id="delete_cust_id" name="delete_cust_id" value="">
                        <div class="confirm-buttons">
                            <button type="submit" name="delete_cust" class="btn">Delete</button>
                            <button type="button" onclick="closeConfirmDialog()" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
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
                    <?php if (table_exists($conn, 'customer')): ?>
                        <?php
                        $search = isset($_GET['customer_search']) ? $_GET['customer_search'] : '';
                        if (!empty($search)) {
                            $stmt = $conn->prepare("SELECT * FROM customer WHERE CustomerID LIKE ?");
                            $likeSearch = "%$search%";
                            $stmt->bind_param("s", $likeSearch);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else
                            $result = $conn->query("SELECT * FROM customer");

                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()): ?>
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
                                        <button class="btn"
                                            onclick="showCustomerEditForm(
                                            '<?= htmlspecialchars($row['CustomerID']) ?>',
                                            '<?= htmlspecialchars($row['CompanyName']) ?>',
                                            '<?= htmlspecialchars($row['CustomerType']) ?>',
                                            '<?= htmlspecialchars($row['SalesRepID']) ?>',
                                            '<?= htmlspecialchars($row['BillingAddress']) ?>',
                                            '<?= htmlspecialchars($row['ShippingAddress']) ?>',
                                            '<?= htmlspecialchars($row['Email']) ?>',
                                            '<?= htmlspecialchars($row['PhoneNumber']) ?>'
                                            )">Edit</button>
                                        <button class="btn btn-danger" onclick="confirmDeleteCust('<?= htmlspecialchars($row['CustomerID']) ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="color:gray;">No customer records found.</td>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="color:red;">Error: Table 'customer' not found.</td>
                        </tr>
                    <?php endif; ?>
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
                <input type="text" id="searchInput" name="search_supplier" placeholder="Search Supplier ID" value="<?= isset($_GET['search_supplier']) ? htmlspecialchars($_GET['search_supplier']) : '' ?>" />
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
                <input type="text" id="supplier_name" name="supplier_name" required />

                <label for="supp_sales_rep_id">Sales Rep ID</label>
                <input type="text" id="supp_sales_rep_id" name="supp_sales_rep_id" required />

                <label for="supplier_address">Address</label>
                <input type="text" id="supplier_address" name="supplier_address" required />

                <label for="supplier_email">Email</label>
                <input type="text" id="supplier_email" name="supplier_email" required />

                <label for="supplier_phone_number">Phone Number</label>
                <input type="text" id="supplier_phone_number" name="supplier_phone_number" required />

                <button class="btn" type="submit" name="create_supplier">Create</button>
            </form>
        </div>
        <!-- This is the supplier record edit form-->
        <div id="editSupplierForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Supplier Record Form</h2>
                    <div class="form-group">
                        <label>Supplier ID [readonly]</label>
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
                    <button class="btn" type="submit" name="update_supplier">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeSupplierEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <!-- Confirmation Dialog -->
        <div id="confirmDialog" class="confirm-dialog">
            <div class="confirm-content">
                <h3>Confirm Delete</h3>
                <p>Are you sure you want to delete this Supplier Record? This action cannot be undone.</p>
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" id="delete_sup_id" name="delete_sup_id" value="">
                    <div class="confirm-buttons">
                        <button type="submit" name="delete_sup" class="btn">Delete</button>
                        <button type="button" onclick="closeConfirmDialog()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
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
            <tbody>
                <?php if (table_exists($conn, 'supplier')): ?>
                    <?php
                    $search = isset($_GET['search_supplier']) ? $_GET['search_supplier'] : '';
                    if (!empty($search)) {
                        $stmt = $conn->prepare("SELECT * FROM supplier WHERE SupplierID LIKE ?");
                        $likeSearch = "%$search%";
                        $stmt->bind_param("s", $likeSearch);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else
                        $result = $conn->query("SELECT * FROM supplier");

                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["SupplierID"]) ?></td>
                                <td><?= htmlspecialchars($row["SupplierName"]) ?></td>
                                <td><?= htmlspecialchars($row["SalesRepID"]) ?></td>
                                <td><?= htmlspecialchars($row["Address"]) ?></td>
                                <td><?= htmlspecialchars($row["Email"]) ?></td>
                                <td><?= htmlspecialchars($row["PhoneNumber"]) ?></td>
                                <td class="actions-row">
                                    <button class="btn"
                                        onclick="showSupplierEditForm(
                                        '<?= htmlspecialchars($row['SupplierID']) ?>',
                                        '<?= htmlspecialchars($row['SupplierName']) ?>',
                                        '<?= htmlspecialchars($row['SalesRepID']) ?>',
                                        '<?= htmlspecialchars($row['Address']) ?>',
                                        '<?= htmlspecialchars($row['Email']) ?>',
                                        '<?= htmlspecialchars($row['PhoneNumber']) ?>'
                                        )">Edit</button>
                                    <button class="btn btn-danger" onclick="confirmDeleteSup('<?= htmlspecialchars($row['SupplierID']) ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="color:gray;">No supplier records found.</td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="color:red;">Error: Table 'supplier' not found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- LOCATION MANAGEMENT -->
    <div class="dashboard">
        <h1>Locations</h2>
            <div class="actions">
                <button class="btn" onclick="showLocationCreateForm()">Create Location Record</button>
                <form method="GET">
                    <input type="text" id="searchInput" name="location_search" placeholder="Search Location ID" value="<?= isset($_GET['location_search']) ? htmlspecialchars($_GET['location_search']) : '' ?>" />
                    <button class="btn" type="submit">Search</button>
                </form>
            </div>

            <!-- This is the location record create form-->
            <div id="createLocationForm" class="toggle-form">
                <form method="POST">
                    <h2>Create New Location Record</h2>
                    <label for="location_id">Location ID</label>
                    <input type="text" id="location_id" name="location_id" required />

                    <label for="location_address">Address</label>
                    <input type="text" id="location_address" name="location_address" />

                    <label for="location_type">Location Type</label>
                    <input type="text" id="location_type" name="location_type" />

                    <label for="location_name">Location Name</label>
                    <input type="text" id="location_name" name="location_name" />

                    <button class="btn" type="submit" name="create_location">Create</button>
                </form>
            </div>
            <!-- This is the location record edit form-->
            <div id="editLocationForm" class="modal-overlay">
                <form method="POST">
                    <div class="modal-content">
                        <h2>Edit Location Record</h2>
                        <div class="form-group">
                            <label>Location ID [readonly]</label>
                            <input type="text" id="edit_location_id" name="edit_location_id" readonly />
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" id="edit_location_address" name="edit_location_address" />
                            </div>
                            <div class="form-group">
                                <label>Location Type</label>
                                <input type="text" id="edit_location_type" name="edit_location_type" />
                            </div>
                            <div class="form-group">
                                <label>Location Name</label>
                                <input type="text" id="edit_location_name" name="edit_location_name" />
                            </div>
                        </div>
                        <button class="btn" type="submit" name="update_location">Save Changes</button>
                        <button class="btn btn-secondary" onclick="closeLocationEditForm()">Cancel</button>
                    </div>
                </form>
            </div>
            <!-- Confirmation Dialog -->
            <div id="confirmDialog" class="confirm-dialog">
                <div class="confirm-content">
                    <h3>Confirm Delete</h3>
                    <p>Are you sure you want to delete this Location Record? This action cannot be undone.</p>
                    <form method="POST" action="" id="deleteForm">
                        <input type="hidden" id="delete_loc_id" name="delete_loc_id" value="">
                        <div class="confirm-buttons">
                            <button type="submit" name="delete_loc" class="btn">Delete</button>
                            <button type="button" onclick="closeConfirmDialog()" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <table>
                <table>
                    <thead>
                        <tr>
                            <th>Location ID</th>
                            <th>Location Address</th>
                            <th>Location Type</th>
                            <th>Location Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (table_exists($conn, 'locations')): ?>
                            <?php
                            $search = isset($_GET['location_search']) ? $_GET['location_search'] : '';
                            if (!empty($search)) {
                                $stmt = $conn->prepare("SELECT * FROM locations WHERE LocationID LIKE ?");
                                $likeSearch = "%$search%";
                                $stmt->bind_param("s", $likeSearch);
                                $stmt->execute();
                                $result = $stmt->get_result();
                            } else
                                $result = $conn->query("SELECT * FROM locations");

                            if ($result && $result->num_rows > 0):
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row["LocationID"]) ?></td>
                                        <td><?= htmlspecialchars($row["Address"]) ?></td>
                                        <td><?= htmlspecialchars($row["LocationType"]) ?></td>
                                        <td><?= htmlspecialchars($row["LocationName"]) ?></td>
                                        <td class="actions-row">
                                            <button class="btn"
                                                onclick="showLocationEditForm(
                                            '<?= htmlspecialchars($row['LocationID']) ?>',
                                            '<?= htmlspecialchars($row['Address']) ?>',
                                            '<?= htmlspecialchars($row['LocationType']) ?>',
                                            '<?= htmlspecialchars($row['LocationName']) ?>'
                                        )">Edit</button>
                                            <button class="btn btn-danger" onclick="confirmDeleteLoc('<?= htmlspecialchars($row['LocationID']) ?>')">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="color:gray;">No location records found.</td>
                                </tr>
                            <?php endif; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="color:red;">Error: Table 'locations' not found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
    </div>

    <!-- SALES REPRESENTATIVE -->
    <div class="dashboard">
        <h1>Sales Representative</h1>
        <div class="actions">
            <button class="btn" onclick="showSalesCreateForm()">Create Sales Rep Record</button>
            <form method="GET">
                <input type="text" id="searchInput" name="search_sr" placeholder="Search Sales Representative ID" value="<?= isset($_GET['search_sr']) ? htmlspecialchars($_GET['search_sr']) : '' ?>" />
                <button class="btn" type="Submit">Search</button>
            </form>
        </div>

        <!-- This is the sales record create form-->
        <div id="createSalesForm" class="toggle-form">
            <form method="POST">
                <h2>Create New Sales Record</h2>
                <label for="sales_id">Sales Rep ID</label>
                <input type="text" id="sales_id" name="sales_id" required />

                <label for="sales_name">Name</label>
                <input type="text" id="sales_name" name="sales_name" required />

                <label for="sales_email">Email</label>
                <input type="text" id="sales_email" name="sales_email" required />

                <label for="sales_phone_number">Phone Number</label>
                <input type="text" id="sales_phone_number" name="sales_phone_number" required />

                <button class="btn" type="submit" name="create_sr">Create</button>
            </form>
        </div>
        <!-- This is the sales record edit form-->
        <div id="editSalesForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Sales Form</h2>
                    <div class="form-group">
                        <label>Sales ID [readonly]</label>
                        <input type="text" id="edit_sales_id" name="edit_sales_id" readonly />
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
                    <button class="btn" type="submit" name="update_sr">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeSalesEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <!-- Confirmation Dialog -->
        <div id="confirmDialog" class="confirm-dialog">
            <div class="confirm-content">
                <h3>Confirm Delete</h3>
                <p>Are you sure you want to delete this Sales Rep Record? This action cannot be undone.</p>
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" id="delete_sales_id" name="delete_sales_id" value="">
                    <div class="confirm-buttons">
                        <button type="submit" name="delete_sales" class="btn">Delete</button>
                        <button type="button" onclick="closeConfirmDialog()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
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
            <tbody>
                <?php if (table_exists($conn, 'salesrepresentative')): ?>
                    <?php
                    $search = isset($_GET['search_sr']) ? $_GET['search_sr'] : '';
                    if (!empty($search)) {
                        $stmt = $conn->prepare("SELECT * FROM SalesRepresentative WHERE SalesRepID LIKE ?");
                        $likeSearch = "%$search%";
                        $stmt->bind_param("s", $likeSearch);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else
                        $result = $conn->query("SELECT * FROM SalesRepresentative");

                    if ($result && $result->num_rows > 0):
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
                                    <button class="btn btn-danger" onclick="confirmDeleteSales('<?= htmlspecialchars($row['SalesRepID']) ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="color:gray;">No sales rep records found.</td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="color:red;">Error: Table 'salesrepresentative' not found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SCRIPTS -->
    <script>
        function showCustomerCreateForm() {
            const form = document.getElementById('createCustomerForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        function showSupplierCreateForm() {
            const form = document.getElementById('createSupplierForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        function showLocationCreateForm() {
            const form = document.getElementById('createLocationForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
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

        function showCustomerEditForm(customer_id, customer_name, customer_type, sales_rep_id, billing_address, ship_address, cust_email, cust_phone_number) {
            document.getElementById('edit_customer_id').value = customer_id;
            document.getElementById('edit_cust_name').value = customer_name;
            document.getElementById('edit_cust_type').value = customer_type;
            document.getElementById('edit_cust_sales_rep_id').value = sales_rep_id;
            document.getElementById('edit_cust_bill_address').value = billing_address;
            document.getElementById('edit_cust_ship_address').value = ship_address;
            document.getElementById('edit_cust_email').value = cust_email;
            document.getElementById('edit_cust_phone_number').value = cust_phone_number;

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

        function showLocationEditForm(location_id, location_address, location_type, location_name) {
            document.getElementById('edit_location_id').value = location_id;
            document.getElementById('edit_location_address').value = location_address;
            document.getElementById('edit_location_type').value = location_type;
            document.getElementById('edit_location_name').value = location_name;

            document.getElementById('editLocationForm').style.display = 'flex';
        }

        function closeSalesEditForm() {
            document.getElementById('editSalesForm').style.display = 'none';
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

        function confirmDeleteCust(cust_id) {
            document.getElementById('delete_cust_id').value = cust_id;

            document.getElementById('confirmDialog').style.display = 'flex';
        }

        function confirmDeleteSup(sup_id) {
            document.getElementById('delete_sup_id').value = sup_id;

            document.getElementById('confirmDialog').style.display = 'flex';
        }

        function confirmDeleteLoc(loc_id) {
            document.getElementById('delete_loc_id').value = loc_id;

            document.getElementById('confirmDialog').style.display = 'flex';
        }

        function confirmDeleteSales(sales_id) {
            document.getElementById('delete_sales_id').value = sales_id;

            document.getElementById('confirmDialog').style.display = 'flex';
        }

        function closeConfirmDialog() {
            document.getElementById('confirmDialog').style.display = 'none';
        }

        // Auto-hide notifications after 5 seconds
        setTimeout(function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(function(notification) {
                notification.style.display = 'none';
            });
        }, 5000);
    </script>
    </div>
</body>

</html>