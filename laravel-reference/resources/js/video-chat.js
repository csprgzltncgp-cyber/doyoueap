import * as Video from 'twilio-video';
import * as axios from 'axios';
import Swal from 'sweetalert2';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const userVideo = document.getElementById('userVideo');
const partnerVideo = document.getElementById('partnerVideo');
const spinner = document.getElementById('spinner');
const unmuteButton = document.getElementById('unmute_button');
const muteButton = document.getElementById('mute_button');
const endButton = document.getElementById('end_button');

let videoTrack = null;
const dataTrack = new Video.LocalDataTrack();

let room = null;

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(async function () {
        await connect();

        unmuteButton.addEventListener('click', unmute);
        muteButton.addEventListener('click', mute);
        endButton.addEventListener('click', endTherapy);

        muteButton.classList.remove('d-none');
        muteButton.classList.add('d-flex');

        endButton.classList.remove('d-none');
        endButton.classList.add('d-flex');
    }, 3000);
});

async function connect() {
    try {
        await checkDevicePermissions();
        await addLocalVideo();
        await connectToRoom();
    } catch (error) {
        alert(error);
        console.log(error);

        spinner.classList.add('d-flex');
        spinner.classList.remove('d-none');
    }
}

async function addLocalVideo() {
    if (!userVideo) {
        return;
    }

    videoTrack = await Video.createLocalVideoTrack();
    const trackElement = videoTrack.attach();
    userVideo.appendChild(trackElement);
}

async function getAccessToken() {
    const response = await axios.post('/ajax/video-therapy/token', {
        room_id,
    });

    return response.data.token;
}

async function connectToRoom() {
    const accessToken = await getAccessToken();

    const localTracks = await Video.createLocalTracks({
        audio: true,
        video: { width: 1280, height: 720 },
    });

    room = await Video.connect(accessToken, {
        logLevel: 'debug',
        name: room_id,
        tracks: [dataTrack, ...localTracks],
    });

    room.participants.forEach(participantConnected);
    room.on('participantConnected', participantConnected);
    room.on('participantDisconnected', participantDisconnected);
}

function participantConnected(participant) {
    const tracksDiv = document.createElement('div');
    tracksDiv.setAttribute('id', participant.sid);
    partnerVideo.appendChild(tracksDiv);

    participant.tracks.forEach((publication) => {
        if (publication.isSubscribed) {
            trackSubscribed(tracksDiv, publication.track);
        }
    });

    participant.on('trackSubscribed', function (track) {
        trackSubscribed(tracksDiv, track);
        handleTrackDisabled(track);
        handleTrackEnabled(track);
    });

    participant.on("subscribed", handleTrackDisabled);
    participant.on("subscribed", handleTrackEnabled);

    participant.on('trackUnsubscribed', trackUnsubscribed);

    spinner.classList.add('d-none');
    spinner.classList.remove('d-flex');
}

function participantDisconnected(participant) {
    document.getElementById(participant.sid).remove();

    document.getElementById('connected_placeholder').classList.remove('hidden');
}

function trackSubscribed(div, track) {
    const trackElement = track.attach();
    div.appendChild(trackElement);
}

function trackUnsubscribed(track) {
    track.detach().forEach((element) => {
        element.remove();
    });
}

function handleTrackDisabled(track) {
    track.on("disabled", () => {
        document.querySelector("#partnerVideo video").classList.add("hidden");
        document
            .getElementById("connected_placeholder")
            .classList.remove("hidden");
    });
}

function handleTrackEnabled(track) {
    track.on("enabled", () => {
        document
            .querySelector("#partnerVideo video")
            .classList.remove("hidden");
        document
            .getElementById("connected_placeholder")
            .classList.add("hidden");
    });
}

function mute() {
    room.localParticipant.audioTracks.forEach((publication) => {
        publication.track.disable();
    });

    room.localParticipant.videoTracks.forEach((publication) => {
        publication.track.disable();
    });

    muteButton.classList.add('d-none');
    muteButton.classList.remove('d-flex');

    unmuteButton.classList.add('d-flex');
    unmuteButton.classList.remove('d-none');

    userVideo.classList.add('d-none');
    userVideo.classList.remove('d-flex');
}

function unmute() {
    room.localParticipant.audioTracks.forEach((publication) => {
        publication.track.enable();
    });

    room.localParticipant.videoTracks.forEach((publication) => {
        publication.track.enable();
    });

    muteButton.classList.add('d-flex');
    muteButton.classList.remove('d-none');

    unmuteButton.classList.add('d-none');
    unmuteButton.classList.remove('d-flex');

    userVideo.classList.add('d-flex');
    userVideo.classList.remove('d-none');
}

async function endTherapy() {
    Swal.fire({
        title: endTherapyTitle,
        text: endTherapyText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: endTherapyConfirm,
        cancelButtonText: endTherapyCancel,
    }).then(function (result) {
        if (result.isConfirmed) {
            dataTrack.send('session_ended');

            room.disconnect();

            setBookingEndDate(room_id).then(function() {
                window.close('', '_parent', '');
            });
        }
    });
}

async function setBookingEndDate(room_id)
{
    await axios.post('/ajax/end-therapy', {
        room_id: room_id,
   }).catch(err => {
       console.log('Error while setting booking end date!');
   });
}

async function checkDevicePermissions() {
    const devices = await navigator.mediaDevices.enumerateDevices();

    const hasVideoInput = devices.some(device => device.kind === 'videoinput');
    const hasAudioInput = devices.some(device => device.kind === 'audioinput');

    if (!hasVideoInput || !hasAudioInput) {
        throw new Error(no_device_found_msg);
    }

    try {
        // Try getting user media to check for permissions
        await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    } catch (err) {
        throw new Error(no_device_permission_msg);
    }
}
