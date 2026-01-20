import './bootstrap'
import { createApp } from 'vue'
import Alpine from 'alpinejs'

/**
 * Кастомный лоадер для монтирования Vue компонентов
 * @param {string} selector - CSS селектор элемента для монтирования
 * @param {string|object} component - Название компонента или Vue компонент
 * @param {object} props - Пропсы для компонента
 */
function mountComponent(selector, component, props = {}) {
    const element = document.querySelector(selector)

    if (!element) {
        console.error(`Element not found for selector: ${selector}`)
        return null
    }

    // Если передано название компонента (строка), загружаем динамически
    if (typeof component === 'string') {
        import(`./components/${component}.vue`)
            .then(module => {
                console.log(`Component ${component} loaded successfully`)

                // Создаем новый div внутри контейнера
                const mountPoint = document.createElement('div')
                element.appendChild(mountPoint)

                const app = createApp(module.default, props)
                const instance = app.mount(mountPoint)
                console.log(`Component ${component} mounted to ${selector}`, instance)
            })
            .catch(err => {
                console.error(`Failed to load component ${component}:`, err)
            })
    } else {
        // Если передан сам компонент
        const mountPoint = document.createElement('div')
        element.appendChild(mountPoint)

        const app = createApp(component, props)
        const instance = app.mount(mountPoint)
        return instance
    }
}

// Экспортируем mountComponent глобально для использования в админке
window.mountComponent = mountComponent

// Автоматическое монтирование компонентов при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    // DepositsTable
    const depositsTableEl = document.querySelector('.js-deposits-table')
    if (depositsTableEl) {
        import('./components/DepositsTable.vue').then(module => {
            const dataUrl = depositsTableEl.dataset.url || '/dashboard/transactions/deposits/data'
            mountComponent('.js-deposits-table', module.default, { dataUrl })
        })
    }

    // WithdrawTable
    const withdrawTableEl = document.querySelector('.js-withdraw-table')
    if (withdrawTableEl) {
        import('./components/WithdrawTable.vue').then(module => {
            const dataUrl = withdrawTableEl.dataset.url || '/dashboard/transactions/withdraw/data'
            mountComponent('.js-withdraw-table', module.default, { dataUrl })
        })
    }

    // StakingTable
    const stakingTableEl = document.querySelector('.js-staking-table')
    if (stakingTableEl) {
        import('./components/StakingTable.vue').then(module => {
            const dataUrl = stakingTableEl.dataset.url || '/dashboard/staking/data'
            mountComponent('.js-staking-table', module.default, { dataUrl })
        })
    }

    // StakeHistoryTable
    const stakeHistoryTableEl = document.querySelector('.js-stake-history-table')
    if (stakeHistoryTableEl) {
        import('./components/StakeHistoryTable.vue').then(module => {
            const dataUrl = stakeHistoryTableEl.dataset.url || '/dashboard/history/data'
            mountComponent('.js-stake-history-table', module.default, { dataUrl })
        })
    }

    // ProfitEarningsTable
    const profitEarningsTableEl = document.querySelector('.js-profit-earnings-table')
    if (profitEarningsTableEl) {
        import('./components/ProfitEarningsTable.vue').then(module => {
            const dataUrl = profitEarningsTableEl.dataset.url || '/dashboard/earnings/profit/data'
            mountComponent('.js-profit-earnings-table', module.default, { dataUrl })
        })
    }

    // EarningsRewardsTable
    const earningsRewardsTableEl = document.querySelector('.js-earnings-rewards-table')
    if (earningsRewardsTableEl) {
        import('./components/EarningsRewardsTable.vue').then(module => {
            const dataUrl = earningsRewardsTableEl.dataset.url || '/dashboard/earnings/rewards/data'
            mountComponent('.js-earnings-rewards-table', module.default, { dataUrl })
        })
    }

    // ReferralsTable
    const referralsTableEl = document.querySelector('.js-referrals-table')
    if (referralsTableEl) {
        import('./components/ReferralsTable.vue').then(module => {
            const dataUrl = referralsTableEl.dataset.url || '/dashboard/referrals/data'
            mountComponent('.js-referrals-table', module.default, { dataUrl })
        })
    }

    // InvestmentPoolTable (old version for full pools page)
    const investmentPoolTableEl = document.querySelector('.js-investment-pool-table')
    if (investmentPoolTableEl) {
        import('./components/InvestmentPoolTable.vue').then(module => {
            const dataUrl = investmentPoolTableEl.dataset.url || '/dashboard/pools/data'
            const balance = parseFloat(investmentPoolTableEl.dataset.balance || 0)
            mountComponent('.js-investment-pool-table', module.default, { dataUrl, balance })
        })
    }

    // PoolsList (simplified for dashboard)
    const poolsListEl = document.querySelector('.js-pools-list')
    if (poolsListEl) {
        import('./components/PoolsList.vue').then(module => {
            const dataUrl = poolsListEl.dataset.url || '/dashboard/pools/data'
            const balance = parseFloat(poolsListEl.dataset.balance || 0)
            mountComponent('.js-pools-list', module.default, { dataUrl, balance })
        })
    }

    // DepositForm
    const depositFormEl = document.querySelector('.js-deposit-form')
    if (depositFormEl) {
        import('./components/DepositForm.vue').then(module => {
            mountComponent('.js-deposit-form', module.default)
        })
    }

    // WithdrawForm
    const withdrawFormEl = document.querySelector('.js-withdraw-form')
    if (withdrawFormEl) {
        import('./components/WithdrawForm.vue').then(module => {
            mountComponent('.js-withdraw-form', module.default)
        })
    }

    // Toast
    const toastEl = document.querySelector('#toast-app')
    if (toastEl) {
        import('./components/Toast.vue').then(module => {
            const app = createApp(module.default)
            const instance = app.mount('#toast-app')

            // Глобальная функция для показа toast
            window.showToast = (message, type = 'info') => {
                if (instance && instance.addToast) {
                    instance.addToast(message, type)
                }
            }

            // Слушатель события show-toast
            window.addEventListener('show-toast', (e) => {
                if (e.detail && instance && instance.addToast) {
                    instance.addToast(e.detail.message, e.detail.type || 'info')
                }
            })
        })
    }
})

// Инициализация Alpine.js - должна быть ДО загрузки DOM, но определение window.Alpine до start()
window.Alpine = Alpine

// Alpine Store for global user data
document.addEventListener('alpine:init', () => {
    Alpine.store('userBalance', {
        balance: 0,
        availableBalance: 0,
        hasPendingWithdraw: false,
        pendingWithdrawAmount: 0,

        init() {
            this.refresh();
            // Refresh balance every 60 seconds
            setInterval(() => this.refresh(), 60000);
        },

        async refresh() {
            try {
                const response = await axios.get('/dashboard/balance/state');
                if (response.data.success) {
                    this.balance = response.data.balance;
                    this.availableBalance = response.data.available_balance;
                    this.hasPendingWithdraw = response.data.has_pending_withdraw;
                    this.pendingWithdrawAmount = response.data.pending_withdraw_amount;
                }
            } catch (error) {
                console.error('Failed to refresh balance:', error);
            }
        }
    });
});

Alpine.start()
