<?php
namespace Aws;

use GuzzleHttp\Command\Exception\CommandException;

/**
 * Represents an AWS exception that is thrown when a command fails.
 */
class AwsException extends CommandException
{
    /**
     * Create a wrapped exception from a CommandException
     *
     * @param CommandException $previous Command exception to wrap
     *
     * @return self
     * @throws \InvalidArgumentException if the wrapped command has no client.
     */
    public static function wrap(CommandException $previous)
    {
        $client = $previous->getClient();
        if (!($client instanceof AwsClientInterface)) {
            throw new \InvalidArgumentException('The wrapped exception must use'
                . ' an AwsClientInterface');
        }

        $message = 'AWS (' . $client->getApi()->getEndpointPrefix()  . ') Error: ';

        if ($prev = $previous->getContext()->getPath('aws_error/message')) {
            $message .= $prev;
        } else {
            $message .= $previous->getMessage();
        }

        return new static(
            $message,
            $previous->getCommandTransaction(),
            $previous
        );
    }

    /**
     * Get the name of the web service that encountered the error.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->getClient()->getApi()->getMetadata('endpointPrefix');
    }

    /**
     * Get the service description model of the web service.
     *
     * @return \Aws\Common\Api\Service
     */
    public function getApi()
    {
        return $this->getClient()->getApi();
    }

    /**
     * Get the request ID of the error. This value is only present if a
     * response was received and is not present in the event of a networking
     * error.
     *
     * @return string|null Returns null if no response was received
     */
    public function getAwsRequestId()
    {
        return $this->getContext()->getPath('aws_error/request_id');
    }

    /**
     * Get the AWS error type.
     *
     * @return string|null Returns null if no response was received
     */
    public function getAwsErrorType()
    {
        return $this->getContext()->getPath('aws_error/type');
    }

    /**
     * Get the AWS error code.
     *
     * @return string|null Returns null if no response was received
     */
    public function getAwsErrorCode()
    {
        return $this->getContext()->getPath('aws_error/code');
    }

    /**
     * Get the request ID of the error. This value is only present if a
     * response was received and is not present in the event of a networking
     * error.
     *
     * @return string|null Returns null if no response was received
     * @deprecated Use getAwsRequestId() instead
     */
    public function getRequestId()
    {
        return $this->getContext()->getPath('aws_error/request_id');
    }

    /**
     * Get the exception code
     *
     * @return string|null
     * @deprecated Use getAwsErrorCode() instead
     */
    public function getExceptionCode()
    {
        return $this->getContext()->getPath('aws_error/code');
    }

    /**
     * Get the exception type (one of client or server)
     *
     * @return string|null
     * @deprecated Use getAwsErrorType() instead
     */
    public function getExceptionType()
    {
        return $this->getContext()->getPath('aws_error/type');
    }
}