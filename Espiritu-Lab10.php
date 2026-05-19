<?php
// =========================================================
// DATABASE CONNECTION (XAMPP / MySQL)
// =========================================================
$host = "localhost";
$user = "root";
$password = "";
$dbname = "pizza_db";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// =========================================================
// HANDLE POST REQUESTS (CRUD OPERATIONS)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -------------------------
    // PIZZA ADMIN
    // -------------------------
    if (isset($_POST['add_pizza'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);

        mysqli_query($conn, "
            INSERT INTO pizzas (name, price)
            VALUES ('$name', $price)
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['update_pizza'])) {
        $id = intval($_POST['item_id']);
        $new_price = floatval($_POST['new_price']);

        mysqli_query($conn, "
            UPDATE pizzas
            SET price = $new_price
            WHERE id = $id
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['delete_pizza'])) {
        $id = intval($_POST['item_id']);

        mysqli_query($conn, "
            DELETE FROM pizzas
            WHERE id = $id
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // -------------------------
    // TOPPINGS ADMIN
    // -------------------------
    if (isset($_POST['add_topping'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);

        mysqli_query($conn, "
            INSERT INTO toppings (name, price)
            VALUES ('$name', $price)
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['update_topping'])) {
        $id = intval($_POST['item_id']);
        $new_price = floatval($_POST['new_price']);

        mysqli_query($conn, "
            UPDATE toppings
            SET price = $new_price
            WHERE id = $id
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['delete_topping'])) {
        $id = intval($_POST['item_id']);

        mysqli_query($conn, "
            DELETE FROM toppings
            WHERE id = $id
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // -------------------------
    // CREATE ORDER
    // -------------------------
    if (isset($_POST['create_order'])) {
        $customer = mysqli_real_escape_string($conn, $_POST['customer']);
        $pizza_id = intval($_POST['pizza_id']);
        $qty = intval($_POST['qty']);

        // Get selected pizza
        $pizza_result = mysqli_query($conn, "
            SELECT name, price
            FROM pizzas
            WHERE id = $pizza_id
        ");
        $pizza = mysqli_fetch_assoc($pizza_result);

        $pizza_name = $pizza['name'];
        $pizza_price = $pizza['price'];

        // Get toppings
        $topping_names = array();
        $toppings_total = 0;

        if (isset($_POST['toppings'])) {
            foreach ($_POST['toppings'] as $topping_id) {
                $topping_id = intval($topping_id);

                $topping_result = mysqli_query($conn, "
                    SELECT name, price
                    FROM toppings
                    WHERE id = $topping_id
                ");

                if ($topping = mysqli_fetch_assoc($topping_result)) {
                    $topping_names[] = $topping['name'];
                    $toppings_total += $topping['price'];
                }
            }
        }

        // Build order details
        $order_details = $pizza_name;

        if (!empty($topping_names)) {
            $order_details .= " + " . implode(", ", $topping_names);
        }

        // Calculate total
        $total = ($pizza_price + $toppings_total) * $qty;

        // Save order
        mysqli_query($conn, "
            INSERT INTO orders (customer, order_details, total, status)
            VALUES ('$customer', '$order_details', $total, 'Pending')
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // -------------------------
    // UPDATE ORDER STATUS
    // -------------------------
    if (isset($_POST['update_status'])) {
        $id = intval($_POST['item_id']);

        mysqli_query($conn, "
            UPDATE orders
            SET status = 'Completed'
            WHERE id = $id
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // -------------------------
    // DELETE ORDER
    // -------------------------
    if (isset($_POST['delete_order'])) {
        $id = intval($_POST['item_id']);

        mysqli_query($conn, "
            DELETE FROM orders
            WHERE id = $id
        ");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🍕 Pizza Master Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #FF6B6B 0%, #FFA500 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: #333;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        h1 { font-size: 3em; margin-bottom: 10px; }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .full-width { grid-column: 1 / -1; }

        @media(max-width: 800px) {
            .grid-layout { grid-template-columns: 1fr; }
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .card h2 {
            color: #FF6B6B;
            border-bottom: 3px solid #FFA500;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: flex-end;
        }

        .form-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="number"] {
            padding: 10px;
            border: 2px solid #FF6B6B;
            border-radius: 8px;
            width: 100%;
        }

        .radio-group,
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .selection-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            background: #fff5f5;
        }

        .selection-item:hover {
            background-color: #ffe8e8;
        }

        .selection-item input {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            accent-color: #FF6B6B;
        }

        .price {
            color: #FFA500;
            font-weight: bold;
            margin-left: 5px;
        }

        button {
            padding: 10px 15px;
            background: #FF6B6B;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #FFA500;
        }

        .btn-large {
            width: 100%;
            padding: 15px;
            font-size: 1.1em;
        }

        .btn-update {
            background: #4CAF50;
            padding: 6px 12px;
            font-size: 0.9em;
        }

        .btn-delete {
            background: #f44336;
            padding: 6px 12px;
            font-size: 0.9em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        th {
            background-color: #FFF5E6;
            color: #FF6B6B;
        }

        .price-input {
            width: 90px !important;
            padding: 6px !important;
            margin-right: 5px;
            border: 1px solid #ccc !important;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
        }

        .bg-pending { background-color: #FFA500; }
        .bg-completed { background-color: #4CAF50; }
    </style>
</head>
<body>
<div class="container">

    <header>
        <h1>🍕 Pizza Master Dashboard</h1>
        <p>Admin Menu Management & Live Ordering System</p>
    </header>

    <!-- PIZZA & TOPPINGS MANAGEMENT -->
    <div class="grid-layout">

        <!-- Manage Pizzas -->
        <div class="card">
            <h2>⚙️ Manage Pizzas</h2>

            <form method="post" class="form-group">
                <div style="flex:2;">
                    <input type="text" name="name" placeholder="New Pizza Name" required>
                </div>
                <div style="flex:1;">
                    <input type="number" name="price" step="0.01" min="0" placeholder="Price" required>
                </div>
                <button type="submit" name="add_pizza">Add</button>
            </form>

            <table>
                <tbody>
                    <?php
                $result = mysqli_query($conn, "SELECT * FROM pizzas ORDER BY id DESC");

                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $name = htmlspecialchars($row['name']);
                    $price = number_format($row['price'], 2);

                    echo "
                    <tr>
                        <td><strong>$name</strong></td>
                        <td>
                            <form method='post' style='display:flex;'>
                                <input type='hidden' name='item_id' value='$id'>
                                <input type='number' name='new_price'
                                       value='$price'
                                       step='0.01'
                                       class='price-input'
                                       required>
                                <button type='submit'
                                        name='update_pizza'
                                        class='btn-update'>Save</button>
                            </form>
                        </td>
                        <td>
                            <form method='post'>
                                <input type='hidden' name='item_id' value='$id'>
                                <button type='submit'
                                        name='delete_pizza'
                                        class='btn-delete'>✖</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Manage Toppings -->
        <div class="card">
            <h2>⚙️ Manage Toppings</h2>

            <form method="post" class="form-group">
                <div style="flex:2;">
                    <input type="text" name="name" placeholder="New Topping Name" required>
                </div>
                <div style="flex:1;">
                    <input type="number" name="price" step="0.01" min="0" placeholder="Price" required>
                </div>
                <button type="submit" name="add_topping">Add</button>
            </form>

            <table>
                <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM toppings ORDER BY id DESC");

                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $name = htmlspecialchars($row['name']);
                    $price = number_format($row['price'], 2);

                    echo "
                    <tr>
                        <td><strong>$name</strong></td>
                        <td>
                            <form method='post' style='display:flex;'>
                                <input type='hidden' name='item_id' value='$id'>
                                <input type='number' name='new_price'
                                       value='$price'
                                       step='0.01'
                                       class='price-input'
                                       required>
                                <button type='submit'
                                        name='update_topping'
                                        class='btn-update'>Save</button>
                            </form>
                        </td>
                        <td>
                            <form method='post'>
                                <input type='hidden' name='item_id' value='$id'>
                                <button type='submit'
                                        name='delete_topping'
                                        class='btn-delete'>✖</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PLACE ORDER -->
    <div class="card" style="max-width:800px; margin:0 auto 30px auto;">
        <h2>🛒 Place New Order</h2>

        <form method="post">
            <div class="form-stack">
                <label><strong>Customer Name</strong></label>
                <input type="text" name="customer" required>
            </div>

            <div class="grid-layout" style="gap:20px; margin-bottom:0;">

                <!-- Pizza Selection -->
                <div class="form-stack">
                    <label><strong>Select Pizza</strong></label>
                    <div class="radio-group">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM pizzas");

                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['id'];
                            $name = htmlspecialchars($row['name']);
                            $price = number_format($row['price'], 2);

                            echo "
                            <label class='selection-item'>
                                <input type='radio'
                                       name='pizza_id'
                                       value='$id'
                                       required>
                                $name
                                <span class='price'>₱$price</span>
                            </label>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Toppings Selection -->
                <div class="form-stack">
                    <label><strong>Select Toppings</strong></label>
                    <div class="checkbox-group">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM toppings");

                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['id'];
                            $name = htmlspecialchars($row['name']);
                            $price = number_format($row['price'], 2);

                            echo "
                            <label class='selection-item'>
                                <input type='checkbox'
                                       name='toppings[]'
                                       value='$id'>
                                $name
                                <span class='price'>+₱$price</span>
                            </label>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-stack" style="margin-top:15px;">
                <label><strong>Quantity</strong></label>
                <input type="number" name="qty" min="1" value="1" required>
            </div>

            <button type="submit"
                    name="create_order"
                    class="btn-large">
                🚀 Submit Order
            </button>
        </form>
    </div>

    <!-- ORDERS TABLE -->
    <div class="card full-width">
        <h2>📋 Live Kitchen Orders</h2>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Order Details</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");

                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $customer = htmlspecialchars($row['customer']);
                    $details = htmlspecialchars($row['order_details']);
                    $total = number_format($row['total'], 2);
                    $status = $row['status'];

                    $badge_class = ($status === 'Completed')
                        ? 'bg-completed'
                        : 'bg-pending';

                    echo "
                    <tr>
                        <td>$id</td>
                        <td>$customer</td>
                        <td>$details</td>
                        <td>₱$total</td>
                        <td>
                            <span class='badge $badge_class'>$status</span>
                        </td>
                        <td style='display:flex; gap:5px;'>";

                    if ($status === 'Pending') {
                        echo "
                        <form method='post'>
                            <input type='hidden'
                                   name='item_id'
                                   value='$id'>
                            <button type='submit'
                                    name='update_status'
                                    class='btn-update'>✔</button>
                        </form>";
                    }

                    echo "
                            <form method='post'>
                                <input type='hidden'
                                       name='item_id'
                                       value='$id'>
                                <button type='submit'
                                        name='delete_order'
                                        class='btn-delete'>✖</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>