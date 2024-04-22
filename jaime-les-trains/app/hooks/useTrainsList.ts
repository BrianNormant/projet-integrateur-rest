import { useEffect, useState } from "react";

export function useTrainsList() {
    console.log("test")
    fetch('https://equipe500.tch099.ovh/projet6/api/trains')
    .then((res) => {
        return res.json();
    })
    .then((data) => {
        console.log(data);
    });
}