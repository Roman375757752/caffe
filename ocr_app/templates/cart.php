<?php
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['userid'])) {
    echo "You must be logged in to view the cart.";
    exit;
}

// Отображение товаров в корзине
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<h1>Your Cart</h1>";
    echo "<ul>";
    foreach ($_SESSION['cart'] as $product) {
        echo "<li>{$product['name']} - {$product['price']}$</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Your cart is empty.</p>";
}
?>

<?php
// Очистка корзины
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}
?>

<form method="POST">
    <button type="submit" name="clear_cart">Clear Cart</button>
</form>
