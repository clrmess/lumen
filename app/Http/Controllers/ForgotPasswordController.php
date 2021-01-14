<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{

    public function __construct()
    {
        $this->broker = 'users';
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $this->validate($request, ['email' => 'required|email']);

        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink($request->only('email'));

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->getSendResetLinkEmailSuccessResponse();

            case Password::INVALID_USER:
            default:
                return $this->getSendResetLinkEmailFailureResponse();
        }
    }

    /**
     * Get the response for after the reset link has been successfully sent.
     *
     * @return JsonResponse
     */
    protected function getSendResetLinkEmailSuccessResponse(): JsonResponse
    {
        return response()->json(['success' => true]);
    }

    /**
     * Get the response for after the reset link could not be sent.
     *
     * @return JsonResponse
     */
    protected function getSendResetLinkEmailFailureResponse(): JsonResponse
    {
        return response()->json(['success' => false]);
    }


    /**
     * Reset the given user's password.
     *
     * @param string $token
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(string $token, Request $request): JsonResponse
    {
        $this->validate($request, $this->getResetValidationRules());

        $credentials = $request->only(
            'email', 'password', 'password_confirmation'
        );
        $credentials = array_merge($credentials, ['token' => $token]);


        $broker = $this->getBroker();

        $response = Password::broker($broker)->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->getResetSuccessResponse();

            default:
                return $this->getResetFailureResponse();
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function getResetValidationRules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * Reset the given user's password.
     *
     * @param User $user
     * @param string $password
     * @return JsonResponse
     */
    protected function resetPassword(User $user, string $password): JsonResponse
    {
        $user->password = app('hash')->make($password);

        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Get the response for after a successful password reset.
     *
     * @return JsonResponse
     */
    protected function getResetSuccessResponse(): JsonResponse
    {
        return response()->json(['success' => true]);
    }

    /**
     * Get the response for after a failing password reset.
     *
     * @return JsonResponse
     */
    protected function getResetFailureResponse(): JsonResponse
    {
        return response()->json(['success' => false]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return string|null
     */
    public function getBroker(): ?string
    {
        return property_exists($this, 'broker') ? $this->broker : null;
    }

}
