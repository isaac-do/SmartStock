<?php
$conn = new mysqli("localhost", "root", "", "smartstock");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_sr'])) {
    $sales_id = $_POST['sales_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("INSERT INTO SalesRepresentative (SalesRepID, Name, Email, PhoneNumber) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $sales_id, $name, $email, $phone_number);
    $stmt->execute();
    $stmt->close();

    header("Location: salesrep.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_sr'])) {
    $sales_id = $_POST['edit_sales_id'];
    $name = $_POST['edit_sales_name'];
    $email = $_POST['edit_sales_email'];
    $phone_number = $_POST['edit_sales_phone_number'];

    $stmt = $conn->prepare("UPDATE SalesRepresentative SET SalesRepID=?, Name=?, Email=?, PhoneNumber=? WHERE SalesRepID=?");
    $stmt->bind_param("sssis", $sales_id, $name, $email, $phone_number, $to_id);
    $stmt->execute();
    $stmt->close();

    header("Location: salesrep.php");
    exit;
}

if (isset($_GET['delete'])) {
    $sales_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM SalesRepresentative WHERE SalesRepID = ?");
    $stmt->bind_param("s", $sales_id);
    $stmt->execute();
    $stmt->close();

    header("Location: salesrep.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transfer Orders | SmartStock ERP</title>
    <link rel="stylesheet" href="style.css" />
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

    <div class="dashboard">
        <h1>Transfer Orders</h1>
        <div class="actions">
            <button class="btn" onclick="showCreateForm()">Create Transfer Order</button>
            <form method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search Transfer ID" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <button class="btn" type="Submit">Search</button>
            </form>
        </div>

        <!-- This is the TO create form-->
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
        <!-- This is the TO edit form-->
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
                                onclick="showEditForm(
                                '<?= htmlspecialchars($row['SalesRepID']) ?>',
                                '<?= htmlspecialchars($row['Name']) ?>',
                                '<?= htmlspecialchars($row['Email']) ?>',
                                '<?= htmlspecialchars($row['PhoneNumber']) ?>',
                                )">Edit</button>
                            <a class="btn btn-danger" href="salesrep.php?delete=<?= urlencode($row['SalesRepID']) ?>"
                                onclick="return confirm('Are you sure you want to delete this Purchase Order?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
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
        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>

</html>