import { mount } from '@vue/test-utils';
import SubmitOrder from '@/Components/SubmitOrder.vue';

describe('SubmitOrder.vue', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = mount(SubmitOrder, {
            props: {
                success: null, // Add props if needed
            },
        });
    });

    it('calculates total amount correctly', async () => {
        // Add items to form
        await wrapper.setData({
            form: {
                items: [
                    { unit_price: 10, quantity: 2 },
                    { unit_price: 20, quantity: 1 },
                ],
            },
        });

        // Trigger reactivity and calculate total
        expect(wrapper.vm.totalAmount).toBe('40.00'); // Adjust according to your calculation logic
    });

    it('shows success message when order is submitted successfully', async () => {
        // Simulate a successful submission
        wrapper.vm.successMessage = 'Order submitted successfully!';

        await wrapper.vm.$nextTick(); // Wait for DOM updates
        expect(wrapper.find('.bg-green-100').exists()).toBe(true); // Check for success message
        expect(wrapper.text()).toContain('Order submitted successfully!');
    });
});