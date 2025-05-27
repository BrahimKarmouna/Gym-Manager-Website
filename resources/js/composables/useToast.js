// Simple toast notification system
export function useToast() {
  const showToast = (message, type = 'info', duration = 3000) => {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300 ease-in-out`;
    
    // Set background color based on type
    if (type === 'success') {
      toast.classList.add('bg-green-600', 'text-white');
    } else if (type === 'error') {
      toast.classList.add('bg-red-600', 'text-white');
    } else if (type === 'warning') {
      toast.classList.add('bg-yellow-500', 'text-white');
    } else {
      toast.classList.add('bg-blue-600', 'text-white');
    }
    
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-2');
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 300);
    }, duration);
    
    // Initial animation
    setTimeout(() => {
      toast.classList.add('translate-y-0');
    }, 100);
  };
  
  return {
    success: (message) => showToast(message, 'success'),
    error: (message) => showToast(message, 'error'),
    warning: (message) => showToast(message, 'warning'),
    info: (message) => showToast(message, 'info')
  };
}
