<?php
namespace app\controller;

use app\model\Config;
use think\facade\View;
use app\controller\Tools;
use app\controller\Notify;

class AdminSetting extends AdminBase
{
    public function baseIndex()
    {
        View::assign('switch', Config::class('switch'));
        View::assign('register', Config::class('register'));
        return View::fetch('../app/view/admin/setting/index.html');
    }

    public function baseSave()
    {
        $class = input('class');

        if ($class == 'notify') {
            $list = ['email_notify', 'telegram_notify'];
        } elseif ($class == 'register') {
            $list = ['allow_public_reg', 'reg_email_veriy'];
        }

        foreach ($list as $item)
        {
            $setting = Config::where('item', $item)->find();
            $setting->value = input($item);
            $setting->save();
        }

        return json(Tools::msg('1', '保存结果', '保存成功'));
    }

    public function emailIndex()
    {
        View::assign('smtp', Config::class('smtp'));
        return View::fetch('../app/view/admin/setting/email.html');
    }

    public function emailSave()
    {
        $list = ['smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_name', 'smtp_sender'];

        foreach ($list as $item)
        {
            if (input($item) == '') {
                return json(Tools::msg('0', '保存失败', '请填写所有项目'));
            }

            $setting = Config::where('item', $item)->find();
            $setting->value = input($item);
            $setting->save();
        }

        return json(Tools::msg('1', '保存结果', '保存成功'));
    }

    public function emailPushTest()
    {
        $recipient = input('recipient');

        if ($recipient == '') {
            return json(Tools::msg('0', '发送失败', '请填写收件人'));
        }

        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return json(Tools::msg('0', '发送失败', '邮箱格式不规范'));
        }

        try {
            Notify::email($recipient, '测试邮件', '这是一封测试邮件。如果你能收到，则可确认邮件推送功能工作正常');
        } catch (\Exception $e) {
            return json(Tools::msg('0', '发送失败', $e->getMessage()));
        }

        return json(Tools::msg('1', '发送结果', '发送成功'));
    }

    public function telegramPushTest()
    {
        $recipient = (int) input('recipient');

        if ($recipient == '') {
            return json(Tools::msg('0', '发送失败', '请填写收信用户 uid'));
        }

        try {
            Notify::telegram($recipient, '这是一条测试消息。如果你能收到，则可确认 Telegram 推送功能工作正常');
        } catch (\Exception $e) {
            return json(Tools::msg('0', '发送失败', $e->getMessage()));
        }

        return json(Tools::msg('1', '发送结果', '发送成功'));
    }

    public function telegramIndex()
    {
        View::assign('telegram', Config::class('telegram'));
        return View::fetch('../app/view/admin/setting/telegram.html');
    }

    public function telegramSave()
    {
        $list = ['telegram_account', 'telegram_token'];

        foreach ($list as $item)
        {
            if (input($item) == '') {
                return json(Tools::msg('0', '保存失败', '请填写所有项目'));
            }

            $setting = Config::where('item', $item)->find();
            $setting->value = input($item);
            $setting->save();
        }

        return json(Tools::msg('1', '保存结果', '保存成功'));
    }

    public function customIndex()
    {
        View::assign('custom', Config::class('custom'));
        return View::fetch('../app/view/admin/setting/custom.html');
    }

    public function customSave()
    {
        $list = ['custom_text', 'custom_script'];

        foreach ($list as $item)
        {
            $setting = Config::where('item', $item)->find();
            $setting->value = input($item);
            $setting->save();
        }

        return json(Tools::msg('1', '保存结果', '保存成功'));
    }
}
