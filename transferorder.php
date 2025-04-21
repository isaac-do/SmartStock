<?php
$conn = new mysqli("localhost", "root", "", "smartstock");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_to'])) {
    $to_id = $_POST['to_id'];
    $transfer_date = $_POST['transfer_date'];
    $from_location = $_POST['from_location'];
    $to_location = $_POST['to_location'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("INSERT INTO TransferOrders (TransferID, TransferDate, FromLocation, ToLocation, ItemID, Quantity) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $to_id, $transfer_date, $from_location, $to_location, $item_id, $quantity);
    $stmt->execute();
    $stmt->close();

    header("Location: transferorder.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_to'])) {
    $to_id = $_POST['edit_to_id'];
    $transfer_date = $_POST['edit_transfer_date'];
    $from_location = $_POST['edit_from_location'];
    $to_location = $_POST['edit_to_location'];
    $item_id = $_POST['edit_item_id'];
    $quantity = $_POST['edit_quantity'];

    $stmt = $conn->prepare("UPDATE TransferOrders SET TransferID=?, TransferDate=?, FromLocation=?, ToLocation=?, ItemID=?, Quantity=? WHERE TransferID=?");
    $stmt->bind_param("sssssis", $to_id, $transfer_date, $from_location, $to_location, $item_id, $quantity, $to_id);
    $stmt->execute();
    $stmt->close();

    header("Location: transferorder.php");
    exit;
}

if (isset($_GET['delete'])) {
    $to_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM TransferOrders WHERE TransferID = ?");
    $stmt->bind_param("s", $to_id);
    $stmt->execute();
    $stmt->close();

    header("Location: transferorder.php");
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
        <div id="createForm" class="toggle-form">
            <form method="POST">
                <h2>Create New Transfer Order</h2>
                <label for="to_id">TransferID</label>
                <input type="text" id="to_id" name="to_id" required />

                <label for="transfer_date">Transfer Date</label>
                <input type="date" id="transfer_date" name="transfer_date" required/>

                <label for="from_location">From Location</label>
                <input type="text" id="from_location" name="from_location" required/>

                <label for="to_location">To Location</label>
                <input type="text" id="to_location" name="to_location" required/>

                <label for="item_id">Item ID</label>
                <input type="text" id="item_id" name="item_id" required/>

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required/>

                <button class="btn" type="submit" name="create_to" onclick="alert('TO Created.')">Create</button>
            </form>
        </div>
        <!-- This is the TO edit form-->
        <div id="editForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Purchase Order</h2>
                    <div class="form-group">
                        <label>TransferID</label>
                        <input type="text" id="edit_to_id" name="edit_to_id" readonly />
                    </div>
                    <div class="form-group">
                        <label>Transfer Date</label>
                        <input type="date" id="edit_transfer_date" name="edit_transfer_date" />
                    </div>
                    <div class="form-group">
                        <label>From Location</label>
                        <input type="text" id="edit_from_location" name="edit_from_location" />
                    </div>
                    <div class="form-group">
                        <label>To Location</label>
                        <input type="text" id="edit_to_location" name="edit_to_location" />
                    </div>
                    <div class="form-group">
                        <label>Item ID</label>
                        <input type="text" id="edit_item_id" name="edit_item_id" />
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="edit_quantity" name="edit_quantity" />
                    </div>
                    <button class="btn" type="submit" name="update_to" onclick="alert('Changes saved.')">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Transfer ID</th>
                    <th>Transfer Date</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>Item ID</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <!--Temporary data. Will need to implement PHP to pull from database.-->
            <tbody id="toTable">
                <?php
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if (!empty($search)) {
                    $stmt = $conn->prepare("SELECT * FROM TransferOrders WHERE TransferID LIKE ?");
                    $likeSearch = "%$search%";
                    $stmt->bind_param("s", $likeSearch);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } 
                else
                    $result = $conn->query("SELECT * FROM TransferOrders");

                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["TransferID"]) ?></td>
                        <td><?= htmlspecialchars($row["TransferDate"]) ?></td>
                        <td><?= htmlspecialchars($row["FromLocation"]) ?></td>
                        <td><?= htmlspecialchars($row["ToLocation"]) ?></td>
                        <td><?= htmlspecialchars($row["ItemID"]) ?></td>
                        <td><?= htmlspecialchars($row["Quantity"]) ?></td>
                        <td class="actions-row">
                            <button class="btn"
                                onclick="showEditForm(
                                '<?= htmlspecialchars($row['TransferID']) ?>',
                                '<?= htmlspecialchars($row['TransferDate']) ?>',
                                '<?= htmlspecialchars($row['FromLocation']) ?>',
                                '<?= htmlspecialchars($row['ToLocation']) ?>',
                                '<?= htmlspecialchars($row['ItemID']) ?>',
                                '<?= htmlspecialchars($row['Quantity']) ?>'
                                )">Edit</button>
                            <a class="btn btn-danger" href="transferorder.php?delete=<?= urlencode($row['TransferID']) ?>"
                                onclick="return confirm('Are you sure you want to delete this Purchase Order?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        function showCreateForm() {
            const form = document.getElementById('createForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        function showEditForm(to_id, transfer_date, from_location, to_location, item_id, quantity) {
            document.getElementById('edit_to_id').value = to_id;
            document.getElementById('edit_transfer_date').value = transfer_date;
            document.getElementById('edit_from_location').value = from_location;
            document.getElementById('edit_to_location').value = to_location;
            document.getElementById('edit_item_id').value = item_id;
            document.getElementById('edit_quantity').value = quantity;
            document.getElementById('editForm').style.display = 'flex';
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>

</html>