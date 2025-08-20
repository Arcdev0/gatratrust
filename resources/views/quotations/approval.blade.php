<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation Approval Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 6px;
            margin-bottom: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #0d6efd;
        }

        .qr-box img {
            border: 1px solid #dee2e6;
            padding: 6px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Quotation Approval Verification</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Quotation Details --}}
                    <div class="col-md-6 mb-3">
                        <div class="section-title">Quotation Details</div>
                        <p><strong>Quotation No:</strong> {{ $quotation->quo_no }}</p>
                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($quotation->date)->format('d M Y') }}</p>
                        <p><strong>Customer:</strong> {{ $quotation->customer_name }}</p>
                    </div>

                    {{-- Approval Details --}}
                    <div class="col-md-6 mb-3">
                        <div class="section-title">Approval Details</div>
                        <p><strong>Approved by:</strong> {{ $approval['approver_name'] ?? '-' }}</p>
                        <p><strong>Position:</strong> {{ $approval['approver_position'] ?? '-' }}</p>
                        <p><strong>Approval Date:</strong> {{ $approval['approval_date'] ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info">
                        <p class="mb-1">
                            This is an official digital approval for
                            <strong>Quotation {{ $quotation->quo_no }}</strong>.
                        </p>
                        <small>Approval ID: <code>{{ $approval['signature_token'] }}</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
