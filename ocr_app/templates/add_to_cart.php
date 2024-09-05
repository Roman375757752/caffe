<?php
session_start();

// Список товаров
$products = [
    1 => ['name' => 'Green Tea', 'price' => 0.25, 'old_price' => 0.30, 'img' => 'стакан.png'],
    2 => ['name' => 'Black Tea', 'price' => 0.25, 'old_price' => 0.25, 'img' => 'стакан.png'],
    3 => ['name' => 'Chamomile Tea', 'price' => 0.25, 'old_price' => 0.25, 'img' => 'стакан.png'],
    4 => ['name' => 'Mint Tea', 'price' => 0.30, 'old_price' => 0.30, 'img' => 'стакан.png'],
    5 => ['name' => 'Berry Tea', 'price' => 0.35, 'old_price' => 0.35, 'img' => 'стакан.png'],
    6 => ['name' => 'Americano Coffee', 'price' => 0.35, 'old_price' => 0.35, 'img' => 'стакан.png'],
    7 => ['name' => 'Cappuccino Coffee', 'price' => 0.35, 'old_price' => 0.35, 'img' => 'стакан.png'],
    8 => ['name' => 'Latte Coffee', 'price' => 0.35, 'old_price' => 0.35, 'img' => 'стакан.png'],
    9 => ['name' => 'Cocoa', 'price' => 0.35, 'old_price' => 0.35, 'img' => 'стакан.png'],
    10 => ['name' => 'Hot Chocolate', 'price' => 0.35, 'old_price' => 0.35, 'img' => 'стакан.png'],
];

// Проверка авторизации
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
    exit;
}

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['id'])) {
        $productId = (int)$input['id'];

        if (isset($products[$productId])) {
            $product = $products[$productId];

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += 1; // Увеличиваем количество
            } else {
                $_SESSION['cart'][$productId] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'img' => $product['img'],
                    'quantity' => 1
                ];
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        }
    } elseif (isset($input['action'])) {
        $action = $input['action'];
        $productId = (int)$input['id'];

        if ($action === 'delete') {
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                echo json_encode(['success' => true, 'message' => 'Product removed from cart.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
            }
        } elseif ($action === 'increase' || $action === 'decrease') {
            if (isset($_SESSION['cart'][$productId])) {
                if ($action === 'increase') {
                    $_SESSION['cart'][$productId]['quantity'] += 1;
                } elseif ($action === 'decrease') {
                    $_SESSION['cart'][$productId]['quantity'] -= 1;
                    if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                        unset($_SESSION['cart'][$productId]);
                    }
                }
                echo json_encode(['success' => true, 'message' => 'Quantity updated.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
            }
        }
    }
    exit;
}

// Вычисление общей стоимости товаров в корзине
$totalPrice = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .cart-container {
            width: 60%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .cart-item img {
            width: 50px;
            height: auto;
        }

        .cart-item h3 {
            margin: 0;
        }

        .cart-item .price {
            color: #d9534f;
            font-weight: bold;
        }

        .total-price {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
        }

        .empty-cart {
            text-align: center;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h2>Your Cart</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <div class="quantity">Quantity: <?php echo $item['quantity']; ?></div>
                    <div class="price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                </div>
            <?php endforeach; ?>
            <div class="total-price">
                Total Price: $<?php echo number_format($totalPrice, 2); ?>
            </div>
        <?php else: ?>
            <div class="empty-cart">Your cart is empty.</div>
        <?php endif; ?>
    </div>
</body>
</html>