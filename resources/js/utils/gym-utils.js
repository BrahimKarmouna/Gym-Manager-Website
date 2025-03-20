import axios from 'axios';

/**
 * Get the authenticated user's primary gym
 * If the user doesn't have a gym, creates one
 * @returns {Promise<Object>} The primary gym object { id, name, location }
 */
export const getUserPrimaryGym = async () => {
  try {
    // Get user's gyms
    const response = await axios.get('/api/gyms/my-gyms');
    
    if (response.data.gyms && response.data.gyms.length > 0) {
      // Return the first gym (primary gym)
      return response.data.gyms[0];
    } else {
      // If no gyms, trigger the backend to create one and assign it
      const assignResponse = await axios.get('/api/gyms/assign-me-to-all');
      
      if (assignResponse.data.gym_count > 0) {
        // Get gyms again to get the newly created one
        const refreshResponse = await axios.get('/api/gyms/my-gyms');
        if (refreshResponse.data.gyms && refreshResponse.data.gyms.length > 0) {
          return refreshResponse.data.gyms[0];
        }
      }
      
      // If we still don't have a gym, create a fallback object
      // This shouldn't happen, but just in case
      console.error('Failed to get or create a primary gym for the user');
      return { id: null, name: 'Default Gym', location: 'Unknown' };
    }
  } catch (error) {
    console.error('Error getting user primary gym:', error);
    return { id: null, name: 'Default Gym', location: 'Unknown' };
  }
};

/**
 * Simple function to check if a gym ID is valid
 * @param {number|string} gymId - The gym ID to check
 * @returns {boolean} Whether the gym ID is valid
 */
export const isValidGymId = (gymId) => {
  if (gymId === null || gymId === undefined || gymId === '') {
    return false;
  }
  
  // Convert to number if it's a string
  const id = Number(gymId);
  
  // Check if it's a valid positive number
  return !isNaN(id) && id > 0;
};
