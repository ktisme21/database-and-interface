//Define variables
let currentIndex = 0;
let isSliding = false;

const slider = document.getElementById('slider');
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;

//Function to move the silde advertisement
function moveToSlide(index) {
    isSliding = true;
    slider.style.transition = 'none'; // Disable the transition
    slider.style.transform = `translateX(-${index * (100 / totalSlides)}%)`;
    
    // Allow for the transition to be re-enabled after the transform takes effect
    setTimeout(() => {
        slider.style.transition = 'transform 0.3s ease';
        isSliding = false;
    }, 10); // 10ms is enough for the transform property to take effect
}

//Event listener for left button click in slide advertisement
document.querySelector('.left-btn').addEventListener('click', () => {
    if (isSliding) return;
    currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalSlides - 1;
    if (currentIndex === totalSlides - 1) {
        // Instant move to the last slide (clone)
        moveToSlide(currentIndex);
    } else {
        slider.style.transform = `translateX(-${currentIndex * (100 / totalSlides)}%)`;
        isSliding = true;
    }
});

//Event listener for right button click in slide advertisement
document.querySelector('.right-btn').addEventListener('click', () => {
    if (isSliding) return;
    currentIndex = (currentIndex < totalSlides - 1) ? currentIndex + 1 : 0;
    if (currentIndex === 0) {
        // Instant move to the first slide (clone)
        moveToSlide(currentIndex);
    } else {
        slider.style.transform = `translateX(-${currentIndex * (100 / totalSlides)}%)`;
        isSliding = true;
    }
});

//Event listener for transistion end
slider.addEventListener('transitionend', () => {
    isSliding = false;
});

//Function to add item to cart
function addToCart(menuItemId) {
    fetch('addToCart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `itemId=${menuItemId}`
    })
    .then(response => response.text())
    .then(data => {
        // alert(data); // Alert the response from addToCart.php
        if (data.includes("successfully")) { // Check if the response indicates success
            window.location.reload(); // Reload the page
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}


//Function to update cart item display in cart panel
function updateCartItemDisplay(itemId, newQuantity) {
    // Find the cart item div using itemId and update its quantity input field
    const itemElement = document.querySelector(`input[data-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.value = newQuantity;
    }
}

//Function to toggle cart panel out in mainpage
function toggleCart() {
    var cartPanel = document.getElementById('cart-panel');
    // Make sure to include 'px' when comparing the styles
    var isOffScreen = cartPanel.style.right === '-400px' || cartPanel.style.right === '';

    if (isOffScreen) {
        cartPanel.style.right = '0px'; // Show the panel
    } else {
        cartPanel.style.right = '-400px'; // Hide the panel
    }
}

//Event listener for DOM ContentLoaded event
document.addEventListener('DOMContentLoaded', function() {
    const cartPanel = document.getElementById('cart-panel');
    const totalPriceElement = document.getElementById('total-price');

    // Listen for clicks within the cart panel
    //Function that links with removeFromCart.php and triggers remove click
    cartPanel.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-btn')) {
            const itemDiv = event.target.closest('.cart-item');
            const itemId = itemDiv.getAttribute('data-item-id');

            fetch('removeFromCart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ itemId: itemId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    itemDiv.remove(); // Remove the item from the DOM
                    totalPriceElement.textContent = `Total: $${data.newTotalPrice}`; // Update the total price displayed
                    alert('Item removed successfully!');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the item.');
            });
        }
    });

    //Function to handle update click
    function handleUpdateClick(itemId, quantity, itemDiv) {
        fetch('updateCartQuantity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ itemId: itemId, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Quantity updated successfully!');
                totalPriceElement.textContent = `Total: $${data.newTotalPrice}`; // Update the total price displayed
            } else {
                alert('Failed to update quantity: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the quantity.');
        });
    }

    //Listen for clicks within the cart panel for updating quantity
    cartPanel.addEventListener('click', function(event) {
        if (event.target.classList.contains('update-btn')) {
            const itemDiv = event.target.closest('.cart-item');
            const itemId = itemDiv.getAttribute('data-item-id');
            const quantityInput = itemDiv.querySelector('.quantity-input');
            const quantity = parseInt(quantityInput.value, 10);
            if (quantity > 0) {
                handleUpdateClick(itemId, quantity, itemDiv);
            } else {
                alert('Quantity must be greater than 0.');
            }
        }
    });
});


