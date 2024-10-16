<form method="POST" action="{{ route('processPayment') }}">
    @csrf
    <input type="text" name="card_number" placeholder="Card Number">
    <input type="text" name="expiration_date" placeholder="Expiration Date (MMYY)">
    <input type="text" name="cvv" placeholder="CVV">
    <input type="text" name="amount" placeholder="Amount">
    <button type="submit">Pay Now</button>
</form>
