<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Página de Include Oculta - Exportação Arquivos TCEMG - CVC.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: CVC.csv.inc.php 59719 2014-09-08 15:00:53Z franver $
  * $Date: 2014-09-08 12:00:53 -0300 (Seg, 08 Set 2014) $
  * $Author: franver $
  * $Rev: 59719 $
  *
*/
/**
 *
 *   CVC.csv | Autor : Lisiane Morais
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGArquivoMensalCVC.class.php";

$rsRecuperaVeiculosCVC10 = new RecordSet();
$rsRecuperaGastosVeiculosCVC20 = new RecordSet();
$rsRecuperaCVC30 = new RecordSet();
$rsRecuperaVeiculosBaixadosCVC40 = new RecordSet();

$obTTCEMGArquivoMensalCVC = new TTCEMGArquivoMensalCVC();
$obTTCEMGArquivoMensalCVC->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGArquivoMensalCVC->setDado('entidades',$stEntidades);
$obTTCEMGArquivoMensalCVC->setDado('mes', $stMes);
$obTTCEMGArquivoMensalCVC->setDado('dt_inicial', $stDataInicial);
$obTTCEMGArquivoMensalCVC->setDado('dt_final', $stDataFinal);

//Tipo Registro 10
$obTTCEMGArquivoMensalCVC->recuperaVeiculos($rsRecuperaVeiculosCVC10);

//Tipo Registro 20
$obTTCEMGArquivoMensalCVC->recuperaGastosVeiculos($rsRecuperaGastosVeiculosCVC20);

 //Tipo Registro 30
$obTTCEMGArquivoMensalCVC->recuperaCVC30($rsRecuperaCVC30);

//Tipo Registro 40
$obTTCEMGArquivoMensalCVC->recuperaVeiculosBaixados($rsRecuperaVeiculosBaixadosCVC40);

 //Tipo Registro 99
$arRecordSetCVC99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecuperaCVC99 = new RecordSet();
$rsRecuperaCVC99->preenche($arRecordSetCVC99);

$inCount=0;

//10 – Cadastro de Veículos ou Equipamentos
if ( count($rsRecuperaVeiculosCVC10->getElementos()) > 0 ) {
     $stChave10 = '';
    
    foreach ($rsRecuperaVeiculosCVC10->getElementos() as $arCVC10) {
        if ($stChave10 != $arCVC10['cod_orgao'].$arCVC10['cod_unidade_sub'].$arCVC10['cod_veiculo']) {   
            
            $inCount++;
            $stChave10 = $arCVC10['cod_orgao'].$arCVC10['cod_unidade_sub'].$arCVC10['cod_veiculo'];
            
            $rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arCVC10));
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco10 );  
     
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_veiculo");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(10);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_veiculo");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subtipo_veiculo");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("marca");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("modelo");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_fabricacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("placa");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("chassi");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_renavam");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_serie");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao_veiculo");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_deslocamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
 
            // 20 – Detalhamento Gasto de Combustível, Lubrificante ou Manutenção por Veículo
            if (count($rsRecuperaGastosVeiculosCVC20->getElementos()) > 0) {
                $stChave20 = '';
                foreach ($rsRecuperaGastosVeiculosCVC20->getElementos() as $arCVC20) {

                    if ($stChave10 == $arCVC20['cod_orgao'].$arCVC20['cod_unidade_sub'].$arCVC20['cod_veiculo']) {   

                        $inCount++;
                        $stChave20 = $arCVC20['cod_orgao'].$arCVC20['cod_unidade_sub'].$arCVC20['cod_veiculo'];
                        $rsBloco20 = 'rsBloco20_'.$inCount;
                        unset($$rsBloco20);
                         
                        $$rsBloco20 = new RecordSet();
                        $$rsBloco20->preenche(array($arCVC20));
                         
                        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG'); 
                        $obExportador->roUltimoArquivo->addBloco( $$rsBloco20 );

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_veiculo");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(10);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("origem_gasto");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_subempenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_empenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_empenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("marcacao_inicial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("marcacao_final");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_gasto");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtde_utilizada");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_gasto");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dsc_pecas_servicos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("atestado_controle");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                        if(count($rsRecuperaCVC30->getElementos()) > 0){
                            $stChave30 = '';
                            foreach ($rsRecuperaCVC30->getElementos() as $arCVC30) {
                                if ($stChave20 == $arCVC30['cod_orgao'].$arCVC30['cod_unidade_sub'].$arCVC30['cod_veiculo']) {                                        
                             
                                    $rsBloco30 = 'rsBloco30_'.$inCount;
                                    unset($$rsBloco30);
                                    $$rsBloco30 = new RecordSet();
                                    $$rsBloco30->preenche(array($arCVC30));
                                  
                                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');                     
                                    $obExportador->roUltimoArquivo->addBloco( $$rsBloco30 );

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_veiculo");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(10);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_estabelecimento");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("localidade");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtde_dias_rodados");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("distacia_estabelecimento");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(11);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_passageiros");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("turnos");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                    //40 – Veículos Baixados
                                    if (count($rsRecuperaVeiculosBaixadosCVC40->getElementos()) > 0) {
                                        foreach ($rsRecuperaVeiculosBaixadosCVC40->getElementos() as $arCVC40) {
                                            if ($stChave30 == $arCVC40['uniorcam_cod_orgao'].$arCVC40['cod_unidadesub'].$arCVC40['cod_veiculo']) {         
                                     
                                                $rsBloco40 = 'rsBloco40_'.$inCount;
                                                unset($$rsBloco40);
                                                $$rsBloco40 = new RecordSet();
                                                $$rsBloco40->preenche(array($arCVC40));

                                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');                     
                                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco40 );

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uniorcam_cod_orgao");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidadesub");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_veiculo");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(10);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descbaixa");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_baixa");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}else{
    $obExportador->roUltimoArquivo->addBloco($rsRecuperaCVC99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecuperaVeiculosCVC10 = null;
$rsRecuperaGastosVeiculosCVC20 = null;
$rsRecuperaCVC30 = null;
$rsRecuperaVeiculosBaixadosCVC40 = null;
$rsRecuperaCVC99 = null;
$obTTCEMGArquivoMensalCVC = null;

?>