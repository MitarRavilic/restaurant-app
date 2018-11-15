function getCartItems(){
    fetch(BASE + 'api/cart', { credentials: 'include' })
        .then(result => result.json())
        .then(data => {
            displayCartItems(data.cartItems);
        });
}

function addItemToCart(id, portion, amount) {
    fetch(BASE + 'api/cart/add/' + id + '/' + portion + '/' + amount + '/' , {
        credentials: 'include',
        headers: new Headers({
            'Content-Type': 'application/json'
        })
      })
        .then(result => result.json())
        .then(data => {
            if (data.error === 'Uspesno dodavanje u sesiju') {
                getCartItems();
            }
        });
}

function clearCartItems() {
    fetch(BASE + 'api/cart/clear', { credentials: 'include'})
        .then(result => result.json())
        .then(data => {
            if (data.error === 'Uspesno brisanje') {
                getCartItems();
            }
        });
}

function displayCartItems(cartItems){
    const cartDiv = document.querySelector('.cart');
    cartDiv.innerHTML = '';

    if(cartItems.length === 0){
        cartDiv.innerHTML = 'Korpa je prazna.';
    }


    for (item of cartItems) {
        const cartLink = document.createElement('a');
        cartLink.style.display = 'block';
        cartLink.innerHTML = item.title + ' Portion: ' + item.portion + ' Amount: ' + item.amount;
        cartLink.href = BASE + 'items/' + item.item_id;
        cartDiv.appendChild(cartLink);
    }

    if(cartItems.length > 0) {
        const loginLink = document.createElement('a');
        loginLink.innerHTML = "Korpa";
        loginLink.href = BASE + 'cart';
        cartDiv.appendChild(loginLink);
    }

}

addEventListener('load', getCartItems);
