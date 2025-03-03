document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".quantity-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            let productId = this.dataset.id;
            let isIncrease = this.classList.contains("plus");
            
            fetch(`/znahidka/core/cart/update_cart.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: productId, action: isIncrease ? "increase" : "decrease" }),
            })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    let quantityElem = btn.parentElement.querySelector(".quantity");
                    let sumElem = btn.closest("tr").querySelector(".sum");
                    let totalPriceElem = document.getElementById("total-price");

                    quantityElem.textContent = data.quantity;
                    sumElem.textContent = data.sum.toFixed(2);
                    totalPriceElem.textContent = data.total.toFixed(2);
                }
            });
        });
    });
});
