<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة الفواتير</title>
    <style>
        @page {
            size: 80mm auto; /* غيّرها لـ 58mm إذا طابعتك أصغر */
            margin: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            padding: 5px;
            margin: 0;
            direction: rtl;
        }

        .bill {
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
            padding-bottom: 10px;
            page-break-inside: avoid;
        }

        .bill strong {
            display: inline-block;
            width: 90px;
        }

        h2 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h2>فواتير المشتركين</h2>

@foreach($bills as $bill)
    <div class="bill">
        <div><strong>الاسم:</strong> {{ $bill->subscriber->name }}</div>
        <div><strong>المولدة:</strong> {{ $bill->subscriber->generator->name ?? '-' }}</div>
        <div><strong>القراءة:</strong> من {{ $bill->old_reading }} إلى {{ $bill->new_reading }}</div>
        <div><strong>الاستهلاك:</strong> {{ $bill->consumption }} ك.و</div>
        <div><strong>المبلغ المستحق:</strong> {{ $bill->amount_due }} ل.س</div>
        <div><strong>المدفوع:</strong> {{ $bill->paid ?? 0 }} ل.س</div>
    </div>
@endforeach

<script>
    window.print();
</script>

</body>
</html>
