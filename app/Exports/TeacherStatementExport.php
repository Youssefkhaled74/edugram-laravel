<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Payment\Entities\Withdraw;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeacherStatementExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private int $teacherId;

    public function __construct(int $teacherId)
    {
        $this->teacherId = $teacherId;
    }

    public function collection(): Collection
    {
        $enrollments = CourseEnrolled::query()
            ->with(['course:id,title,user_id', 'user:id,name,email'])
            ->whereHas('course', function ($query) {
                $query->where('user_id', $this->teacherId);
            })
            ->latest('created_at')
            ->get()
            ->map(function ($enroll) {
                $gross = (float) ($enroll->purchase_price ?? 0);
                $net = (float) ($enroll->reveune ?? 0);
                $commission = max($gross - $net, 0);

                return (object) [
                    'date' => $enroll->created_at,
                    'type' => 'شراء كورس',
                    'course_name' => (string) ($enroll->course->title ?? '-'),
                    'student_name' => (string) ($enroll->user->name ?? $enroll->user->email ?? '-'),
                    'amount' => $gross,
                    'commission' => $commission,
                    'net' => $net,
                    'status' => $this->mapEnrollStatus($enroll),
                    'notes' => !empty($enroll->tracking) ? ('Tracking: ' . $enroll->tracking) : '-',
                ];
            });

        $withdraws = Withdraw::query()
            ->where('instructor_id', $this->teacherId)
            ->latest('created_at')
            ->get()
            ->map(function ($withdraw) {
                return (object) [
                    'date' => $withdraw->created_at,
                    'type' => 'طلب سحب',
                    'course_name' => '-',
                    'student_name' => '-',
                    'amount' => (float) ($withdraw->amount ?? 0),
                    'commission' => 0.0,
                    'net' => (float) ($withdraw->amount ?? 0) * -1,
                    'status' => ((int) $withdraw->status === 1) ? 'مكتمل' : 'قيد الانتظار',
                    'notes' => trim(($withdraw->method ?? '-') . (!empty($withdraw->issueDate) ? (' | تاريخ الصرف: ' . Carbon::parse($withdraw->issueDate)->format('Y-m-d')) : '')),
                ];
            });

        return $enrollments
            ->concat($withdraws)
            ->sortByDesc(function ($row) {
                return optional($row->date)->timestamp ?? 0;
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            'تاريخ العملية',
            'نوع العملية',
            'اسم الكورس',
            'اسم الطالب',
            'قيمة العملية',
            'عمولة المنصة',
            'صافي ربح المدرس',
            'حالة الدفع',
            'ملاحظات',
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->date)->format('Y-m-d H:i') ?? '-',
            $row->type ?? '-',
            $row->course_name ?? '-',
            $row->student_name ?? '-',
            $this->formatAmount($row->amount ?? 0),
            $this->formatAmount($row->commission ?? 0),
            $this->formatAmount($row->net ?? 0),
            $row->status ?? '-',
            $row->notes ?? '-',
        ];
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    private function mapEnrollStatus($enroll): string
    {
        if (isset($enroll->status)) {
            return ((int) $enroll->status === 1) ? 'مدفوع' : 'قيد الانتظار';
        }

        return 'مدفوع';
    }
}

