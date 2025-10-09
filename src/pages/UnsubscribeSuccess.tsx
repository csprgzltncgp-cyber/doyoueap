import { useNavigate } from 'react-router-dom';
import logo from '@/assets/doyoueap-logo.png';

const UnsubscribeSuccess = () => {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-background flex items-center justify-center p-4">
      <div className="text-center space-y-8">
        <h1 className="text-4xl font-bold text-foreground">
          Sikeres leiratkozás!
        </h1>
        <div className="flex justify-center">
          <img 
            src={logo} 
            alt="DoYouEAP" 
            className="h-32 cursor-pointer hover:opacity-80 transition-opacity"
            onClick={() => navigate('/')}
          />
        </div>
      </div>
    </div>
  );
};

export default UnsubscribeSuccess;
