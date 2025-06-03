<!DOCTYPE html>
<html>
<head>
  <title>Midtrans Payment</title>
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body>
  <h1>Bayar Sekarang</h1>

  {{-- <form id="paymentForm"> --}}
    <input type="text" name="name" placeholder="Nama" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="number" name="gross_amount" placeholder="Total Bayar" required><br>
    <button type="submit">Bayar</button>
  </form>

  <script>
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      const formData = new FormData(form);

      const response = await fetch('/api/get-snap-token', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
      });

      const data = await response.json();

      if (data.snap_token) {
        snap.pay(data.snap_token, {
          onSuccess: function(result) {
            alert("Pembayaran berhasil!");
            console.log(result);
          },
          onPending: function(result) {
            alert("Pembayaran sedang diproses.");
            console.log(result);
          },
          onError: function(result) {
            alert("Pembayaran gagal.");
            console.log(result);
          },
          onClose: function() {
            alert("Kamu menutup popup tanpa menyelesaikan pembayaran.");
          }
        });
      } else {
        alert("Gagal mendapatkan snap token");
      }
    });
  </script>
</body>
</html>