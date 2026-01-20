<template>
    <div class="registration-container min-h-screen flex items-center justify-center p-4 md:p-12 font-poppins relative overflow-hidden bg-white">
        <!-- Background elements from Figma -->
        <div class="absolute left-[-530px] top-[-264px] w-[1189px] h-[1189px] opacity-20 pointer-events-none">
            <div class="absolute inset-0 bg-blue-100 rounded-full blur-[100px]"></div>
        </div>
        <div class="absolute right-[-100px] top-[10%] w-[552px] h-[552px] opacity-20 pointer-events-none">
            <div class="absolute inset-0 bg-blue-100 rounded-full blur-[100px]"></div>
        </div>

        <div class="w-full max-w-[1320px] relative z-10 flex flex-col items-center">
            <!-- Header Title -->
            <div class="text-center mb-8">
                <h1 class="text-[#3b4efc] text-[52px] font-extrabold leading-[0.9] mb-4">Sign up</h1>
                <p class="text-[#262262] text-[32px] leading-[1.13]">
                    {{ stepTitle }}
                </p>
            </div>

            <!-- Main Card -->
            <div class="w-full bg-white border border-[#2ba6ff] rounded-[13px] shadow-[0px_4px_4px_0px_rgba(43,166,255,0.25)] p-8 md:p-16 flex flex-col items-center min-h-[600px] transition-all duration-300">
                <!-- Logo -->
                <div class="mb-12">
                    <img src="/img/home/logo-final.png" alt="Luma Stake" class="h-20 w-auto">
                </div>

                <!-- Step 1: Start -->
                <div v-if="step === 1" class="w-full max-w-md flex flex-col items-center">
                    <button @click="nextStep(2)" class="w-full bg-[#E6FF00] text-[#262262] py-4 rounded-md font-medium text-2xl mb-6 flex items-center justify-center gap-3 hover:opacity-90 transition-opacity">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-[#262262]">
                            <i class="fas fa-user text-sm"></i>
                        </span>
                        Sign up with Email
                    </button>

                    <div class="flex items-center w-full mb-6">
                        <div class="flex-1 h-px bg-[#d5d5d5]"></div>
                        <span class="px-4 text-[#000000] text-opacity-70 text-[28px]">or</span>
                        <div class="flex-1 h-px bg-[#d5d5d5]"></div>
                    </div>

                    <a href="/auth/google" class="w-full border border-gray-300 py-4 rounded-md flex items-center justify-center gap-3 text-2xl font-medium mb-12 hover:bg-gray-50 transition-colors">
                        <img src="/img/registration_redesign/fefa1c2e6f5c665029dc6d31c54c0577fef11aa5.svg" alt="Google" class="w-6 h-6">
                        Sign in with Google
                    </a>

                    <p class="text-[28px] text-[#ccc]">
                        Do have an account?
                        <a href="/login" class="text-[#3b4efc] font-semibold hover:underline">Login</a>
                    </p>
                </div>

                <!-- Step 2: Email -->
                <div v-if="step === 2" class="w-full max-w-lg flex flex-col items-center">
                    <div class="w-full mb-8">
                        <input
                            v-model="formData.email"
                            type="email"
                            placeholder="Email"
                            class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-6 py-5 text-2xl focus:outline-none focus:border-opacity-100 transition-all"
                            :class="{'border-red-500': errors.email}"
                        >
                        <p v-if="errors.email" class="text-red-500 mt-2">{{ errors.email }}</p>
                    </div>

                    <div class="flex items-start gap-3 mb-12">
                        <input
                            id="terms"
                            v-model="formData.terms"
                            type="checkbox"
                            class="mt-2 w-6 h-6 rounded border-[#2BA6FF]"
                        >
                        <label for="terms" class="text-[20px] text-black text-opacity-70">
                            By Creating an account, I consent to the Luma Stake
                            <a href="/terms" target="_blank" class="font-semibold text-black">Terms of Service</a>
                            and
                            <a href="/privacy" target="_blank" class="font-semibold text-black">Privacy Policy</a>.
                        </label>
                    </div>

                    <button
                        @click="handleEmailSubmit"
                        :disabled="loading || !formData.terms"
                        class="w-full bg-[#E6FF00] text-[#262262] py-4 rounded-md font-bold text-2xl hover:opacity-90 transition-opacity disabled:opacity-50"
                    >
                        <span v-if="loading">Sending...</span>
                        <span v-else>Next</span>
                    </button>
                </div>

                <!-- Step 3: Verification Code -->
                <div v-if="step === 3" class="w-full max-w-lg flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-[#E5F3FF] flex items-center justify-center mb-6">
                        <i class="far fa-envelope text-[#3b4efc] text-3xl"></i>
                    </div>
                    <h2 class="text-[#3b4efc] text-[40px] font-bold mb-4">Verification Code</h2>
                    <p class="text-[#989898] text-xl mb-12">We’ve sent a 6-digit verification code to {{ formData.email }}</p>

                    <div class="flex gap-3 mb-12">
                        <input
                            v-for="(digit, index) in 6"
                            :key="index"
                            ref="codeInputs"
                            v-model="codeDigits[index]"
                            type="text"
                            maxlength="1"
                            class="w-16 h-20 text-center text-3xl font-bold rounded-md border transition-all focus:outline-none focus:ring-2 focus:ring-[#3b4efc]"
                            :class="codeDigits[index] ? 'bg-[#E5F3FF] border-[#3b4efc] text-[#3b4efc]' : 'bg-[#f8f8f8] border-gray-300'"
                            @input="focusNext(index)"
                            @keydown.backspace="focusPrev(index, $event)"
                        >
                    </div>

                    <button
                        @click="handleCodeVerify"
                        :disabled="loading || codeDigits.join('').length !== 6"
                        class="w-full bg-[#E6FF00] text-[#262262] py-4 rounded-md font-bold text-2xl mb-8 hover:opacity-90 transition-opacity disabled:opacity-50"
                    >
                        <span v-if="loading">Verifying...</span>
                        <span v-else>Continue</span>
                    </button>

                    <p class="text-[#989898] text-lg">
                        Didn't receive the code?
                        <button
                            @click="resendCode"
                            :disabled="resendTimer > 0"
                            class="text-[#3b4efc] font-semibold hover:underline disabled:opacity-50"
                        >
                            Resend Code {{ resendTimer > 0 ? `(${resendTimer}s)` : '' }}
                        </button>
                    </p>
                </div>

                <!-- Step 4: Password -->
                <div v-if="step === 4" class="w-full max-w-lg flex flex-col items-center">
                    <div class="w-full mb-8 text-left space-y-2">
                        <div class="flex items-center gap-2" :class="passValidations.length ? 'text-green-500' : 'text-[#989898]'">
                            <i class="fas fa-check text-sm"></i>
                            <span class="text-xl">At least 8 characters</span>
                        </div>
                        <div class="flex items-center gap-2" :class="passValidations.number ? 'text-green-500' : 'text-[#989898]'">
                            <i class="fas fa-check text-sm"></i>
                            <span class="text-xl">At least 1 number</span>
                        </div>
                        <div class="flex items-center gap-2" :class="passValidations.upper ? 'text-green-500' : 'text-[#989898]'">
                            <i class="fas fa-check text-sm"></i>
                            <span class="text-xl">At least 1 upper case letter</span>
                        </div>
                    </div>

                    <div class="w-full mb-12 relative">
                        <input
                            v-model="formData.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Enter Password"
                            class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-6 py-5 text-2xl focus:outline-none focus:border-opacity-100 transition-all"
                            @input="validatePassword"
                        >
                        <button @click="showPassword = !showPassword" class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>

                    <button
                        @click="handlePasswordSubmit"
                        :disabled="loading || !isPasswordValid"
                        class="w-full bg-[#E6FF00] text-[#262262] py-4 rounded-md font-bold text-2xl hover:opacity-90 transition-opacity disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>

                <!-- Step 5: Promo Code -->
                <div v-if="step === 5" class="w-full max-w-lg flex flex-col items-center">
                    <p class="text-black text-opacity-70 text-xl text-center mb-12">
                        Your account has been created successfully. Set it up now.
                    </p>

                    <div class="w-full mb-12">
                        <input
                            v-model="formData.promo_code"
                            type="text"
                            placeholder="Promo code (Optional)"
                            class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-6 py-5 text-2xl focus:outline-none focus:border-opacity-100 transition-all text-center"
                        >
                    </div>

                    <button
                        @click="nextStep(6)"
                        class="w-full bg-[#E6FF00] text-[#262262] py-4 rounded-md font-bold text-2xl hover:opacity-90 transition-opacity"
                    >
                        Next
                    </button>
                </div>

                <!-- Step 6: Information -->
                <div v-if="step === 6" class="w-full max-w-2xl flex flex-col items-center">
                    <p class="text-black text-opacity-70 text-xl text-center mb-8">
                        Please provide the following Information on your passport or ID Card.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full mb-12">
                        <div class="md:col-span-2">
                            <select v-model="formData.nationality" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-6 py-4 text-xl focus:outline-none">
                                <option value="" disabled>Nationality</option>
                                <option v-for="c in countries" :key="c.code" :value="c.code">{{ c.name }}</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <input v-model="formData.name" type="text" placeholder="Full Name" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-6 py-4 text-xl focus:outline-none">
                        </div>

                        <div class="md:col-span-2">
                            <select v-model="formData.gender" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-6 py-4 text-xl focus:outline-none">
                                <option value="" disabled>Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 text-left">
                            <label class="text-[#989898] block mb-2">Date of Birth:</label>
                            <div class="grid grid-cols-3 gap-4">
                                <input v-model="formData.dob_dd" type="text" placeholder="DD" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-4 py-4 text-xl text-center focus:outline-none">
                                <input v-model="formData.dob_mm" type="text" placeholder="MM" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-4 py-4 text-xl text-center focus:outline-none">
                                <input v-model="formData.dob_yyyy" type="text" placeholder="YYYY" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-4 py-4 text-xl text-center focus:outline-none">
                            </div>
                        </div>

                        <div class="md:col-span-2 flex gap-4">
                            <div class="w-1/3">
                                <select v-model="formData.country_code" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-4 py-4 text-xl focus:outline-none">
                                    <option v-for="c in countries" :key="c.code" :value="c.phone_code">{{ c.code }} ({{ c.phone_code }})</option>
                                </select>
                            </div>
                            <div class="w-2/3">
                                <input v-model="formData.phone" type="tel" placeholder="Phone Number" class="w-full bg-[#E5F3FF] border border-[#2BA6FF] border-opacity-30 rounded-[10px] px-6 py-4 text-xl focus:outline-none">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-[#989898] block mb-2">Account Type:</label>
                            <div class="grid grid-cols-2 gap-4">
                                <button
                                    @click="formData.account_type = 'normal'"
                                    class="py-4 border rounded-md text-xl font-medium transition-all"
                                    :class="formData.account_type === 'normal' ? 'bg-[#3b4efc] text-white border-[#3b4efc]' : 'bg-white text-gray-700 border-gray-300'"
                                >
                                    Normal Account
                                </button>
                                <button
                                    @click="formData.account_type = 'islamic'"
                                    class="py-4 border rounded-md text-xl font-medium transition-all"
                                    :class="formData.account_type === 'islamic' ? 'bg-[#3b4efc] text-white border-[#3b4efc]' : 'bg-white text-gray-700 border-gray-300'"
                                >
                                    Islamic Account
                                </button>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="finalizeRegistration"
                        :disabled="loading"
                        class="w-full bg-[#E6FF00] text-[#262262] py-4 rounded-md font-bold text-2xl hover:opacity-90 transition-opacity disabled:opacity-50"
                    >
                        <span v-if="loading">Processing...</span>
                        <span v-else>Continue</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RegisterForm',
    props: {
        csrfToken: String,
        refCode: String
    },
    data() {
        return {
            step: 1,
            loading: false,
            formData: {
                email: '',
                terms: false,
                password: '',
                promo_code: '',
                name: '',
                nationality: '',
                gender: '',
                dob_dd: '',
                dob_mm: '',
                dob_yyyy: '',
                phone: '',
                country_code: '',
                account_type: 'normal',
                ref: this.refCode || ''
            },
            codeDigits: ['', '', '', '', '', ''],
            errors: {},
            showPassword: false,
            resendTimer: 0,
            countries: [],
            passValidations: {
                length: false,
                number: false,
                upper: false
            }
        }
    },
    computed: {
        stepTitle() {
            const titles = {
                1: 'Welcome to Luma Stake Dashboard',
                2: 'Create Personal Account',
                3: 'Verify Your Email',
                4: 'Set Password',
                5: 'Set up your account',
                6: 'Personal Information'
            }
            return titles[this.step]
        },
        isPasswordValid() {
            return this.passValidations.length && this.passValidations.number && this.passValidations.upper
        }
    },
    mounted() {
        this.loadCountries()
        // If ref is in URL, it's already in refCode prop
    },
    methods: {
        nextStep(s) {
            this.step = s
            window.scrollTo(0, 0)
        },
        async loadCountries() {
            try {
                const response = await fetch('/api/v1/geoip/countries')
                const data = await response.json()
                if (data.success) {
                    this.countries = data.countries
                    // Auto-detect country
                    const geoResponse = await fetch('/api/v1/geoip/country')
                    const geoData = await geoResponse.json()
                    if (geoData.success) {
                        const country = this.countries.find(c => c.code === geoData.country.country_code)
                        if (country) {
                            this.formData.nationality = country.code
                            this.formData.country_code = country.phone_code
                        }
                    }
                }
            } catch (e) {
                console.error('Failed to load countries', e)
            }
        },
        async handleEmailSubmit() {
            if (!this.formData.email) {
                this.errors.email = 'Email is required'
                return
            }
            this.loading = true
            this.errors = {}

            try {
                const response = await fetch('/register/step1-send-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ email: this.formData.email })
                })
                const data = await response.json()
                if (data.success) {
                    this.nextStep(3)
                    this.startResendTimer()
                } else {
                    this.errors.email = data.message || 'Failed to send code'
                    if (window.showToast) window.showToast(this.errors.email, 'error')
                }
            } catch (e) {
                if (window.showToast) window.showToast('Something went wrong', 'error')
            } finally {
                this.loading = false
            }
        },
        focusNext(index) {
            if (this.codeDigits[index] && index < 5) {
                this.$refs.codeInputs[index + 1].focus()
            }
            if (this.codeDigits.join('').length === 6) {
                this.handleCodeVerify()
            }
        },
        focusPrev(index, event) {
            if (!this.codeDigits[index] && index > 0) {
                this.$refs.codeInputs[index - 1].focus()
            }
        },
        async handleCodeVerify() {
            const code = this.codeDigits.join('')
            if (code.length !== 6) return

            this.loading = true
            try {
                const response = await fetch('/register/step2-verify-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({
                        email: this.formData.email,
                        code: code
                    })
                })
                const data = await response.json()
                if (data.success) {
                    this.nextStep(4)
                } else {
                    if (window.showToast) window.showToast(data.message || 'Invalid code', 'error')
                    this.codeDigits = ['', '', '', '', '', '']
                    this.$refs.codeInputs[0].focus()
                }
            } catch (e) {
                if (window.showToast) window.showToast('Verification failed', 'error')
            } finally {
                this.loading = false
            }
        },
        validatePassword() {
            const p = this.formData.password
            this.passValidations.length = p.length >= 8
            this.passValidations.number = /\d/.test(p)
            this.passValidations.upper = /[A-Z]/.test(p)
        },
        handlePasswordSubmit() {
            if (this.isPasswordValid) {
                this.nextStep(5)
            }
        },
        async resendCode() {
            if (this.resendTimer > 0) return
            this.loading = true
            try {
                const response = await fetch('/register/resend-code-api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ email: this.formData.email })
                })
                const data = await response.json()
                if (data.success) {
                    if (window.showToast) window.showToast('Code resent!', 'success')
                    this.startResendTimer()
                }
            } catch (e) {
                if (window.showToast) window.showToast('Failed to resend code', 'error')
            } finally {
                this.loading = false
            }
        },
        startResendTimer() {
            this.resendTimer = 60
            const interval = setInterval(() => {
                this.resendTimer--
                if (this.resendTimer <= 0) clearInterval(interval)
            }, 1000)
        },
        async finalizeRegistration() {
            this.loading = true
            try {
                const response = await fetch('/register/finalize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify(this.formData)
                })
                const data = await response.json()
                if (data.success) {
                    if (window.showToast) window.showToast('Registration complete!', 'success')
                    window.location.href = data.redirect || '/dashboard'
                } else {
                    if (window.showToast) window.showToast(data.message || 'Registration failed', 'error')
                }
            } catch (e) {
                if (window.showToast) window.showToast('Something went wrong', 'error')
            } finally {
                this.loading = false
            }
        }
    }
}
</script>

<style scoped>
.registration-container {
    background: linear-gradient(180deg, #FFFFFF 0%, #F0F7FF 100%);
}
input::placeholder {
    color: rgba(0, 0, 0, 0.3);
}
</style>
