let cart = [];

function addToCart(name, price) {

    let product = cart.find(p => p.name === name);

    if (product) {
        product.qty++;
    } else {
        cart.push({
            name: name,
            price: price,
            qty: 1
        });
    }

    renderCart();
}

function removeFromCart(index) {
    cart.splice(index,1);
    renderCart();
}

function renderCart() {

    const body = document.getElementById("cart-body");
    body.innerHTML = "";

    let total = 0;

    cart.forEach((item, index) => {
        let subtotal = item.price * item.qty;
        total += subtotal;

        body.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>$${item.price}</td>
                <td>
                    <input type="number" min="1" value="${item.qty}"
                        onchange="updateQty(${index}, this.value)"
                        class="form-control form-control-sm"
                        style="width:70px">
                </td>
                <td>$${subtotal}</td>
                <td>
                    <button class="btn btn-danger btn-sm"
                        onclick="removeFromCart(${index})">
                        ❌
                    </button>
                </td>
            </tr>
        `;
    });

    document.getElementById("cart-total").innerText = `$${total}`;
}

function updateQty(index, qty) {
    cart[index].qty = parseInt(qty);
    renderCart();
}
