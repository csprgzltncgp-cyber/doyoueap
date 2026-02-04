import './bootstrap';
import { ZoomMtg } from '@zoom/meetingsdk';

const axios = window.axios;

if (!axios) {
    throw new Error('Axios instance is not available. Make sure bootstrap.js is loaded.');
}

ZoomMtg.preLoadWasm();
ZoomMtg.prepareWebSDK();

const configElement = document.getElementById('zoom-start-config');

if (configElement) {
    const config = JSON.parse(configElement.textContent || '{}');
    let endNotified = false;

    const notifyBackendMeetingEnded = async () => {
        if (endNotified) {
            return;
        }

        endNotified = true;

        if (!config.endEndpoint) {
            return;
        }

        try {
            await axios.post(config.endEndpoint);
        } catch (error) {
            console.warn('Failed to notify backend that webinar ended', error);
        }
    };

    ZoomMtg.inMeetingServiceListener('onMeetingStatus', (payload) => {
        // Status 3 corresponds to "meeting disconnected".
        if (payload?.status === 3) {
            notifyBackendMeetingEnded();
        }
    });

    const joinMeeting = async () => {
        try {
            const signatureResponse = await axios.post(config.signatureEndpoint);
            const signaturePayload = signatureResponse.data;

            console.log(config.signatureEndpoint);


            await new Promise((resolve, reject) => {
                ZoomMtg.init({
                    leaveUrl: config.leaveUrl,
                    patchJsMedia: true,
                    success: resolve,
                    error: reject,
                });
            });

            await new Promise((resolve, reject) => {
                ZoomMtg.join({
                    sdkKey: signaturePayload.sdkKey,
                    signature: signaturePayload.signature,
                    meetingNumber: signaturePayload.meetingNumber,
                    userName: signaturePayload.userName || config.userName || 'Host',
                    userEmail: signaturePayload.userEmail || config.userEmail || '',
                    passWord: signaturePayload.passcode || '',
                    success: resolve,
                    error: reject,
                });
            });
        } catch (error) {
            console.error('Unable to start Zoom webinar', error);
            const messageContainer = document.getElementById('zoom-error');
            if (messageContainer) {
                messageContainer.style.display = 'block';
                messageContainer.textContent = 'Unable to start the Zoom webinar. Please refresh and try again.';
            }
        }
    };

    window.addEventListener('beforeunload', () => {
        notifyBackendMeetingEnded();
    });

    joinMeeting();
}
