# Sammy Tech Test Cases

| ID | Test | Expected Result |
| --- | --- | --- |
| TC-01 | Add two products to cart | Both products and correct total appear. |
| TC-02 | Change cart quantity | Line and cart totals update. |
| TC-03 | Remove cart item | Item is removed and total recalculates. |
| TC-04 | Checkout using delivery | Order, customer, address, and payment records save. |
| TC-05 | Delivery without address | Friendly validation error appears. |
| TC-06 | Confirm order with stock | Stock reduces and a movement is recorded. |
| TC-07 | Cancel confirmed order | Stock is restored. |
| TC-08 | Confirm insufficient stock | Confirmation fails without changing stock. |
| TC-09 | Track with wrong phone | No order details are revealed. |
| TC-10 | Track with correct details | Status, items, payment state, and receipt appear. |
| TC-11 | Invalid proof upload | Unsupported or oversized file is rejected. |
| TC-12 | Installation request | Staff can view and update the request. |

## Demonstration Checklist

1. Add three products to the cart.
2. Check out with delivery and a payment method.
3. Confirm the order as staff.
4. Confirm payment and show stock movement.
5. Track the order and print its receipt.
6. Submit and manage an installation request.
7. Open reports and explain the sales and inventory values.
