<?php


namespace PgAsync\Message;


class Bind implements CommandInterface {
    use CommandTrait;

    private $portalName = "";
    private $statementName = "";

    private $params = [];

    /**
     * Bind constructor.
     * @param array $params
     * @param string $statementName
     */
    public function __construct(array $params, $statementName)
    {
        $this->params        = $params;
        $this->statementName = $statementName;
    }


    public function encodedMessage()
    {
        $message = $this->portalName . "\0";
        $message .= $this->statementName . "\0";

        // next is the number of format codes - we say zero because we are just going to use text
        $message .= Message::int16(0);

//        // this would be where the param codes are added
//        $message = Message::int16(count($this->params));
//        for ($i = 0; $i < count($this->params); $i++) {
//            // we are only going to use strings for right now
//            $message .=
//        }

        // parameter values
        $message .= Message::int16(count($this->params));
        for ($i = 0; $i < count($this->params); $i++) {
            if ($this->params[$i] === null) {
                // null is a special case that just has a length of -1
                $message .= Message::int32(-1);
                continue;
            }
            $message .= Message::int32(strlen($this->params[$i])) . $this->params[$i];
        }

        // result column format codes - we aren't using these right now
        $message .= Message::int16(0);

        return 'B'.Message::prependLengthInt32($message);
    }

    public function shouldWaitForComplete() {
        return false;
    }
}