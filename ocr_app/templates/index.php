<?php
session_start();
$isLoggedIn = isset($_SESSION['userid']); // Проверка, авторизован ли пользователь
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR App</title>
    <style>
        body {
            position: relative;
            background-color: #8C8C88;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            flex-direction: column;
            padding: 20px;
            background-color: #202426;
            position: relative;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
        }

        .logo h1 {
            margin: 0;
        }

        .search-container {
            position: relative;
            text-align: right;
            margin-top: 10px;
            width: 100%;
        }

        .search-container input[type="text"] {
            min-width: 200px;
            max-width: calc(100% - 100px);
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 40px;
            box-sizing: border-box;
            padding-right: 80px;
            transition: width 0.3s ease;
            width: 200px;
        }

        .search-container button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #202426;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 15px;
            cursor: pointer;
        }

        .dropdown-container {
            position: relative;
            margin-top: 10px;
        }

        .dropdown-button {
            display: inline-block;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 10px;
            box-sizing: border-box;
            overflow-y: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dropdown-section {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            width: calc(33.333% - 20px);
            box-sizing: border-box;
            text-align: center;
        }

        .dropdown-section a {
            color: black;
            padding: 8px 0;
            text-decoration: none;
            display: block;
        }

        .dropdown-section a:hover {
            background-color: #ddd;
        }

        .rectangle-area {
            width: 100%;
            max-width: 50%;
            margin: 20px auto;
            padding: 20px;
            background-color: #9DA65D;
            border-radius: 0px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .cart {
    position: relative;
    display: inline-block;
    margin-right: 20px;
    }

    #cart-icon {
        width: 40px;
        height: 40px;
        cursor: pointer;
    }

    #cart-count {
    position: absolute;
    top: 0;
    right: 0;
    background-color: #f74c1d;
    color: white;
    border-radius: 40%;
    padding: 3px;
    font-size: 10px;
    line-height: 1;
    min-width: 10px;
    text-align: center;
}

    .add-to-cart-animation {
        animation: bounce 0.5s ease-in-out;
    }

    @keyframes bounce {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.5);
        }
        100% {
            transform: scale(1);
        }
    }


        .menu {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

        .menu-item {
            width: 200px;
            text-align: center;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .menu-item img {
            width: 100%;
            height: auto;
        }

        .menu-item h3 {
            margin: 10px 0;
        }

        .menu-item p {
            padding: 0 10px;
        }

        .menu-item .price {
            color: #d9534f;
            font-weight: bold;
            margin: 10px 0;
        }

        .menu-item .add-to-cart {
            display: block;
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-top: 1px solid #ddd;
            margin-top: auto;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        form input[type="file"] {
            margin-bottom: 10px;
        }

        form button {
            padding: 10px 20px;
            background-color: #202426;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #333;
        }

        #result {
            text-align: center;
        }
    </style>
    <script>
        function adjustInputWidth() {
            const input = document.querySelector('.search-container input[type="text"]');
            const valueLength = input.value.length;
            input.style.width = ${Math.max(200, valueLength * 8)}px;
        }

        document.addEventListener('input', adjustInputWidth);

        function showDropdown() {
            document.querySelector('.dropdown-content').style.display = 'flex';
        }

        function hideDropdown() {
            document.querySelector('.dropdown-content').style.display = 'none';
        }

        function setupDropdown() {
            const dropdownButton = document.querySelector('.dropdown-button');
            const dropdownContent = document.querySelector('.dropdown-content');

            let hideTimeout;

            dropdownButton.addEventListener('mouseenter', () => {
                clearTimeout(hideTimeout);
                showDropdown();
            });

            dropdownButton.addEventListener('mouseleave', () => {
                hideTimeout = setTimeout(hideDropdown, 300);
            });

            dropdownContent.addEventListener('mouseenter', () => {
                clearTimeout(hideTimeout);
            });

            dropdownContent.addEventListener('mouseleave', () => {
                hideTimeout = setTimeout(hideDropdown, 300);
            });
        }

        window.onload = () => {
            setupDropdown();
        };
    </script>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <div id="welcome-message">You have successfully logged in, <?php echo htmlspecialchars($username); ?>!</div>
    <?php endif; ?>

    <div class="header">
        <div class="header-top">
            <div class="logo">
                <h1>ZoomNetwork</h1>
            </div>

            <div class="cart">
                <img src="cart-icon.png" alt="Cart" id="cart-icon">
                <span id="cart-count">0</span>
            </div>


            <div class="search-container">
                <input type="text" placeholder="Search...">
                <button type="button">Enter</button>
            </div>
        </div>
        <div class="dropdown-container">
            <div class="dropdown-button">Select an option</div>
            <div class="dropdown-content">
                <div class="dropdown-section">
                    <a href="page1.html">Register</a>
                </div>
                <?php if (!$isLoggedIn): ?>
                    <div class="dropdown-section">
                        <a href="page2.html">Login</a>
                    </div>
                <?php endif; ?>
                <div class="dropdown-section">
                    <a href="page3.html">Advertisement</a>
                </div>
                <div class="dropdown-section">
                    <a href="page4.html">For suggestions</a>
                </div>
                <div class="dropdown-section">
                    <a href="user_dashboard.php">My Account</a>
                </div>
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown-section">
                        <a href="logout.php">Logout</a>
                    </div>
                    <div class="dropdown-section">
                        <a href="add_to_cart.php">Add Item</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="rectangle-area">
        <h2>Coffee</h2>
        <div class="menu">

        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Americano Coffee">
                <h3>Americano Coffee</h3>
                <p>A few sips of hot Americano, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <a href="#" class="add-to-cart"  data-id="6">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Green Tea">
                <h3>Green Tea</h3>
                <p>Enjoy the freshness of green tea</p>
                <p class="price">0.25$</p>
                <button disabled class="add-to-cart-disabled">Log in to add to cart</button>
            </div>
        <?php endif; ?>


        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Cappuccino Coffee">
                <h3>Cappuccino Coffee</h3>
                <p>A few sips of hot Cappuccino, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <a href="#" class="add-to-cart"  data-id="7">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Cappuccino Coffee">
                <h3>Cappuccino Coffee</h3>
                <p>A few sips of hot Cappuccino, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <button disabled class="add-to-cart-disabled">Log in to add to cart</button>
            </div>
        <?php endif; ?>
            
        <?php if ($isLoggedIn): ?>    
            <div class="menu-item">
                <img src="стакан.png" alt="Latte Coffee">
                <h3>Latte Coffee</h3>
                <p>A few sips of hot Latte, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <a href="#" class="add-to-cart"  data-id="8">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Cappuccino Coffee">
                <h3>Cappuccino Coffee</h3>
                <p>A few sips of hot Cappuccino, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <button disabled class="add-to-cart-disabled">Log in to add to cart</button>
            </div>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Cocoa">
                <h3>Cocoa</h3>
                <p>A few sips of hot Cocoa, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <a href="#" class="add-to-cart"  data-id="9">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Cappuccino Coffee">
                <h3>Cappuccino Coffee</h3>
                <p>A few sips of hot Cappuccino, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <button disabled class="add-to-cart-disabled">Log in to add to cart</button>
            </div>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Hot Chocolate">
                <h3>Hot Chocolate</h3>
                <p>A few sips of hot Chocolate, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <a href="#" class="add-to-cart" data-id="10">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Cappuccino Coffee">
                <h3>Cappuccino Coffee</h3>
                <p>A few sips of hot Cappuccino, and you are ready to conquer the day</p>
                <p class="price">0.35$</p>
                <button disabled class="add-to-cart-disabled">Log in to add to cart</button>
            </div>
        <?php endif; ?>

        </div>
    </div>

    <div class="rectangle-area">
        <h2>Tea</h2>
        <div class="menu">

        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Green Tea">
                <h3>Green Tea</h3>
                <p>Enjoy the freshness of green tea</p>
                <p class="price">0.25$</p>
                <a href="#" class="add-to-cart" data-id="1">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Green Tea">
                <h3>Green Tea</h3>
                <p>Enjoy the freshness of green tea</p>
                <p class="price">0.25$</p>
                <button disabled class="add-to-cart-disabled">Log in to add to cart</button>
            </div>
        <?php endif; ?>


        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Black Tea">
                <h3>Black Tea</h3>
                <p>Classic black tea for any time of the day</p>
                <p class="price">0.25$</p>
                <a href="#" class="add-to-cart" data-id="2">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Black Tea">
                <h3>Black Tea</h3>
                <p>Classic black tea for any time of the day</p>
                <p class="price">0.25$</p>
                <button disabled class="add-to-cart-disabled">Login in to add to cart</button>
            </div>
        <?php endif; ?>


        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Chamomile Tea">
                <h3>Chamomile Tea</h3>
                <p>Soothing chamomile tea</p>
                <p class="price">0.25$</p>
                <a href="#" class="add-to-cart"  data-id="3">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Chamomile Tea">
                <h3>Chamomile Tea</h3>
                <p>Soothing chamomile tea</p>
                <p class="price">0.25$</p>
                <button disabled class="add-to-cart-disabled">Login in to add to cart</button>
            </div>
        <?php endif; ?>


        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Mint Tea">
                <h3>Mint Tea</h3>
                <p>Refreshing mint tea</p>
                <p class="price">0.30$</p>
                <a href="#" class="add-to-cart" data-id="4">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Mint Tea">
                <h3>Mint Tea</h3>
                <p>Refreshing mint tea</p>
                <p class="price">0.30$</p>
                <button disabled class="add-to-cart-disabled">Login in to add to cart</button>
            </div>
        <?php endif; ?>


        <?php if ($isLoggedIn): ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Berry Tea">
                <h3>Berry Tea</h3>
                <p>Tea with berry aroma</p>
                <p class="price">0.35$</p>
                <a href="#" class="add-to-cart" data-id="5">Add to cart</a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <img src="стакан.png" alt="Berry Tea">
                <h3>Berry Tea</h3>
                <p>Tea with berry aroma</p>
                <p class="price">0.35$</p>
                <button disabled class="add-to-cart-disabled">Login in to add to cart</button>
            </div>
        <?php endif; ?>

        
    </div>

    <script>
        document.querySelector('.dropdown-content').addEventListener('click', function(event) {
            if (event.target.tagName === 'A') {
                var selectedPage = event.target.href; 
                if (selectedPage) {
                    window.location.href = selectedPage;
                }
            }
        });

        
    document.getElementById('cart-icon').addEventListener('click', function() {
        window.location.href = 'add_to_cart.php'; // Перенаправление на страницу add_to_cart.php
    });

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const productId = this.getAttribute('data-id');

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Анимация для иконки корзины
                    const cartIcon = document.getElementById('cart-icon');
                    cartIcon.classList.add('add-to-cart-animation');
                    
                    // Увеличение количества товаров в корзине
                    let cartCount = document.getElementById('cart-count');
                    cartCount.textContent = parseInt(cartCount.textContent) + 1;

                    // Удаляем класс анимации после завершения
                    setTimeout(() => {
                        cartIcon.classList.remove('add-to-cart-animation');
                    }, 500);
                } else {
                    alert('Failed to add product to cart: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
</script>



    </script>
</body>
</html>