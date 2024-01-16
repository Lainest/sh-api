<?php

class EventsController implements Controller
{

    private EventsGateway $gateway;

    public function __construct()
    {
        $this->gateway = new EventsGateway(new Database());
    }

    public function getAll(): void
    {
        $events = $this->gateway->getAll();

        new Response($events, 200);
    }

    public function getOne(string $id): void
    {
        $event = $this->gateway->getById($id);

        if ($event === false) {
            new Response([
                "Error" => "Event not found"
            ], 404);
            return;
        }

        new Response($event, 200);
    }

    public function create(): void
    {
        $body = (array) json_decode(file_get_contents("php://input"), true);
        $validation_errors = $this->validateEventCreation($body);

        if (is_array($validation_errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $validation_errors]);
            die;
        }

        $id = $this->gateway->create($body);

        new Response([
            $id => "Event created"
        ], 201);
    }

    private function validateEventCreation($data): array | false
    {
        $validation = Validation::getInstance();

        $validation->setData($data);

        $validation->field('name')->type('text')->required();
        $validation->field('stadium')->type('text')->required();
        $validation->field('date')->type('datetime')->required();
        $validation->field('expiration')->type('datetime')->required();

        return $validation->hasErrors();
    }
}
