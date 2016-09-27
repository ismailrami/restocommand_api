<?php namespace Tymon\JWTAuth\Providers;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWT as Firebase;
use Exception;

class FirebaseProvider extends AbstractProvider implements ProviderInterface {

	/**
	 * Create a JSON Web Token
	 *
	 * @param mixed $subject
	 * @param array $customClaims
	 * @return \Tymon\JWTAuth\Token
	 * @throws \Tymon\JWTAuth\Exceptions\JWTException
	 */
	public function encode($subject, array $customClaims = [])
	{
		try
		{
			$token = Firebase::encode( $this->buildPayload($subject, $customClaims), $this->secret, $this->algo );
			$this->createToken($token);
		}
		catch (Exception $e)
		{
			throw new JWTException( 'Could not create token: ' . $e->getMessage() );
		}

		return $this->token;
	}

	/**
	 * Decode a JSON Web Token
	 *
	 * @param  string $token
	 * @return \Tymon\JWTAuth\Payload
	 * @throws \Tymon\JWTAuth\Exceptions\JWTException
	 */
	public function decode($token)
	{
		$this->createToken($token);

        try {
            $payload = (array) Firebase::decode($this->token, $this->secret);
        } catch (Exception $e) {
        	// firebase implementation doesn't return the payload if it has expired :(
        	if ($e->getMessage() === 'Expired Token') {
        		throw new TokenExpiredException('JWT has expired');
        	} else {
        		throw new JWTException('Could not decode token: ' . $e->getMessage());
        	}
        }

        return $this->createPayload($payload);
	}

}
