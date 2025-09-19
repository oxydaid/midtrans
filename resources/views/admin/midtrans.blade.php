<div class="row g-3">
    <div class="mb-3 col-md-6">
        <label class="form-label" for="keyInput">Client Key</label>
        <input type="text" class="form-control @error('client-key') is-invalid @enderror" id="keyInput" name="client-key" value="{{ old('client-key', $gateway->data['client-key'] ?? '') }}" required>

        @error('client-key')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="keyInput">Server Key</label>
        <input type="text" class="form-control @error('server-key') is-invalid @enderror" id="keyInput" name="server-key" value="{{ old('server-key', $gateway->data['server-key'] ?? '') }}" required>

        @error('server-key')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label for="keyInput" class="form-label">Mode</label>
        <select name="mode" id="keyInput" class="form-select @error('mode') is-invalid @enderror" required>
            <option value="sandbox" @selected(old('mode', $gateway->data['mode'] ?? '')=='sandbox')>Sandbox</option>
            <option value="production" @selected(old('mode', $gateway->data['mode'] ?? '')=='production')>Production</option>
        </select>

        @error('mode')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Cara Menyambungkan ke Midrans</h4>
            <ol>
                <li>Daftar akun Midtrans di <a href="https://midtrans.com/" target="_blank" class="text-decoration-underline text-danger">midtrans.com</a></li>
                <li>Isi semua form yang diperlukan oleh Midtrans</li>
                <li>Copy Client Key dan Server Key lalu isikan pada form di atas</li>
                <li>Copy url callback lalu isikan pada pengaturan > payment > notification url</li>
            </ol>
            <span>url callback: <code>{{ route('shop.payments.notification', ['midtrans', '12']) }}</code></span><br>
            <small>Jika ada kendala hubungi developer plugin ini di <a href="https://wa.me/6285258688255" target="_blank">Whatsapp</a>.</small>
        </div>
    </div>
</div>