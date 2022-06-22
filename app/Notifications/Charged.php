<?php

namespace App\Notifications;

use App\Models\FeeCharge;
use EasyPost\Fee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Charged extends Notification implements ShouldQueue
{
    use Queueable;

    public $feeCharge;
    public $status;

    const StatusMap = [
        //'success' => '成功',
        //'failed' => '失败',
        FeeCharge::STATUS_RETURNED => '审核未通过！',
        FeeCharge::STATUS_AUDITING => '已提交审核，请审核！',
        FeeCharge::STATUS_SETTLED => '已操作完成！请查看余额明细！',
    ];

    public function __construct(FeeCharge $feeCharge, $status)
    {
        $this->feeCharge = $feeCharge;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $type = $this->feeCharge->type == FeeCharge::TYPE_EXPEND ? '扣费' : '入账';
        return [
            'status' => $this->status,
            'title' => $type,
            'url' => '/financial/charge?number=' . $this->feeCharge->number,
            'message' => $type . '申请编号为' . $this->feeCharge->number . self::StatusMap[$this->status],
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
