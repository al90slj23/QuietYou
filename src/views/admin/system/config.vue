<template>
  <div class="page-container">
    <div class="config-card">
      <t-tabs v-model="activeTab">
        <t-tab-panel value="basic" label="基础配置">
          <t-form :data="basicConfig" label-width="140px" class="config-form">
            <t-form-item label="平台名称"><t-input v-model="basicConfig.siteName" /></t-form-item>
            <t-form-item label="平台简介"><t-textarea v-model="basicConfig.siteDesc" :autosize="{ minRows: 3 }" /></t-form-item>
            <t-form-item label="客服电话"><t-input v-model="basicConfig.servicePhone" /></t-form-item>
            <t-form-item label="客服微信"><t-input v-model="basicConfig.serviceWechat" /></t-form-item>
            <t-form-item label="备案号"><t-input v-model="basicConfig.icp" /></t-form-item>
          </t-form>
        </t-tab-panel>

        <t-tab-panel value="order" label="订单配置">
          <t-form :data="orderConfig" label-width="140px" class="config-form">
            <t-form-item label="订单超时时间"><t-input-number v-model="orderConfig.payTimeout" :min="5" suffix="分钟" /></t-form-item>
            <t-form-item label="自动确认时间"><t-input-number v-model="orderConfig.autoConfirm" :min="1" suffix="小时" /></t-form-item>
            <t-form-item label="评价有效期"><t-input-number v-model="orderConfig.reviewDays" :min="1" suffix="天" /></t-form-item>
          </t-form>
        </t-tab-panel>

        <t-tab-panel value="commission" label="佣金配置">
          <t-form :data="commissionConfig" label-width="140px" class="config-form">
            <t-form-item label="平台佣金比例"><t-input-number v-model="commissionConfig.platformRate" :min="0" :max="100" suffix="%" /></t-form-item>
            <t-form-item label="技师最低提现"><t-input-number v-model="commissionConfig.techMinWithdraw" :min="0" prefix="¥" /></t-form-item>
            <t-form-item label="商户最低提现"><t-input-number v-model="commissionConfig.merchantMinWithdraw" :min="0" prefix="¥" /></t-form-item>
            <t-form-item label="结算周期"><t-input-number v-model="commissionConfig.settleDays" :min="1" suffix="天" /></t-form-item>
          </t-form>
        </t-tab-panel>

        <t-tab-panel value="sms" label="短信配置">
          <t-form :data="smsConfig" label-width="140px" class="config-form">
            <t-form-item label="短信服务商">
              <t-select v-model="smsConfig.provider">
                <t-option value="aliyun" label="阿里云" />
                <t-option value="tencent" label="腾讯云" />
              </t-select>
            </t-form-item>
            <t-form-item label="AccessKey"><t-input v-model="smsConfig.accessKey" type="password" /></t-form-item>
            <t-form-item label="SecretKey"><t-input v-model="smsConfig.secretKey" type="password" /></t-form-item>
            <t-form-item label="签名"><t-input v-model="smsConfig.signName" /></t-form-item>
          </t-form>
        </t-tab-panel>
      </t-tabs>

      <div class="form-actions">
        <t-button theme="primary" @click="handleSave">保存配置</t-button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Tabs as TTabs, TabPanel as TTabPanel, Form as TForm, FormItem as TFormItem,
  Input as TInput, Textarea as TTextarea, InputNumber as TInputNumber,
  Select as TSelect, Option as TOption, Button as TButton, MessagePlugin
} from 'tdesign-vue-next'

const activeTab = ref('basic')

const basicConfig = reactive({
  siteName: '轻养到家',
  siteDesc: '专业上门按摩服务平台，连接优质技师与用户',
  servicePhone: '400-888-8888',
  serviceWechat: 'qingyangdaojia',
  icp: '蜀ICP备XXXXXXXX号'
})

const orderConfig = reactive({
  payTimeout: 30,
  autoConfirm: 24,
  reviewDays: 7
})

const commissionConfig = reactive({
  platformRate: 10,
  techMinWithdraw: 100,
  merchantMinWithdraw: 500,
  settleDays: 7
})

const smsConfig = reactive({
  provider: 'aliyun',
  accessKey: '',
  secretKey: '',
  signName: '轻养到家'
})

const handleSave = () => {
  MessagePlugin.success('配置保存成功')
}
</script>

<style lang="scss" scoped>
.config-card { background: #fff; border-radius: 8px; padding: 24px; }
.config-form { max-width: 600px; padding: 24px 0; }
.form-actions { padding-top: 24px; border-top: 1px solid #f0f0f0; }
</style>
