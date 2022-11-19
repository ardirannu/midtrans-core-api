# Payment using Midtrans with core api method

In this case the payment is made with the BCA bank, the product details are made static on the payment controller.

- before submitting payment, setup midtrans url notification first.
- after making a payment, please pay using the Midtrans Simulator.
- to handle midtrans notifications on the local server, we can use a webhook to catch notifications from midtrans after paying.
- please set your webhook url on the midtrans configuration menu.

## API Reference
#### Creating Payment

```http
  POST /buy/product
```
| Parameter        | Type     | Description                       |
| :--------        | :------- | :-------------------------------- |
| `payment_method` | `string` | **Required**. Ex: bank_transfer   |


#### After making a payment
#### Post Midtrans Notification Response that appears in the webhook.

```http
  POST /notification/push
```
