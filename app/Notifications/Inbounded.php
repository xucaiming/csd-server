<?php

namespace App\Notifications;

use App\Models\InboundOrder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class Inbounded extends Notification implements ShouldQueue
{
    use Queueable;

    public $inboundOrder;
    public $status;

    const StatusMap = [
        'auditing' => '已提交审核',
        'audited' => '审核已通过，等待仓库接收',
        'exceptional' => '审核未通过，请咨询客服',
        'inbounded' => '已全部完成入库',
        'part_inbounded' => '已部分入库，具体请查看入库记录'
    ];

    public function __construct(InboundOrder $inboundOrder, $status = 'auditing')
    {
        $this->inboundOrder = $inboundOrder;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        // 如果同时需要广播，返回数组加上'broadcast'
        return [
            'database',
            'broadcast',
        ];
    }

    public function toArray($notifiable)
    {
        // 存入数据表的数据
        return [
            'status' => $this->status,
            'title' => '入库',
            'url' => '/inbound/index?number=' . $this->inboundOrder->number . '&t=' . microtime(true),
            'message' => '入库编号为' . $this->inboundOrder->number . self::StatusMap[$this->status],
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
