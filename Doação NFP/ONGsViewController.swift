//
//  ViewController.swift
//  Doação NFP
//
//  Created by Joao Pedro on 5/9/16.
//  Copyright © 2016 Joao Pedro. All rights reserved.
//

import UIKit

class ONGsViewController: UITableViewController{
    
    var ongs:[ONG] = [ONG(nome: "ONG 1",id: "1010",info: "Informacoes sobre a ONG 1"),
                      ONG(nome: "ONG 2",id: "1011",info: "Informacoes sobre a ONG 2"),
                      ONG(nome: "ONG 3",id: "1100",info: "Informacoes sobre a ONG 3"),
                      ONG(nome: "ONG 4",id: "1101",info: "Informacoes sobre a ONG 4"),
                      ONG(nome: "ONG 5",id: "1110",info: "Informacoes sobre a ONG 5"),
                      ONG(nome: "ONG 6",id: "1111",info: "Informacoes sobre a ONG 6"),
                      ONG(nome: "ONG 7",id: "0010",info: "Informacoes sobre a ONG 7")]
    var qrcode = "";
    @IBOutlet weak var newBtn: UIBarButtonItem!
    
    func setupForModalView(withQR qr:String){
        qrcode = qr
        self.title = "Selecione a ONG"
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return ongs.count
    }
    
    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("ONGCell", forIndexPath: indexPath)
        cell.textLabel?.text = ongs[indexPath.row].Nome
        return cell
    }
    
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        
        let storyboard = UIStoryboard(name: "Main", bundle: nil)
        let vc = storyboard.instantiateViewControllerWithIdentifier("ongDetail") as! ONGDetailViewController
        vc.ong = ongs[indexPath.row]
        vc.qrcode = self.qrcode
        self.navigationController?.pushViewController(vc, animated: true)
        tableView.deselectRowAtIndexPath(indexPath, animated: false)
    }
    
    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        return 1
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
}
