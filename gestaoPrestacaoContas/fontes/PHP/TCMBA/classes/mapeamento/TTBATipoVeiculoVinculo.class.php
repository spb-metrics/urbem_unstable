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
    * Data de Criação: 19/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: TTBATipoVeiculoVinculo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBATipoVeiculoVinculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBATipoVeiculoVinculo()
{
    parent::Persistente();
    $this->setTabela('tcmba.tipo_veiculo_vinculo');

    $this->setCampoCod('cod_tipo_tcm');
    $this->setComplementoChave('cod_tipo');

    $this->AddCampo('cod_tipo_tcm','integer',true,'',true,'TTBATipoVeiculo');
    $this->AddCampo('cod_tipo','integer',true,'',true,'TFrotaTipoVeiculo');
}

function recuperaTipoVeiculoVinculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaTipoVeiculoVinculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaTipoVeiculoVinculo()
{
    $stSql = "
        SELECT tcmba.tipo_veiculo.cod_tipo_tcm
             , tcmba.tipo_veiculo.descricao AS nom_tipo_tcm
             , frota_tipo_veiculo.cod_tipo AS cod_tipo_sw
             , frota_tipo_veiculo.nom_tipo AS nom_tipo_sw
          FROM tcmba.tipo_veiculo_vinculo
    INNER JOIN tcmba.tipo_veiculo
            ON tipo_veiculo.cod_tipo_tcm = tipo_veiculo_vinculo.cod_tipo_tcm
    INNER JOIN frota.tipo_veiculo AS frota_tipo_veiculo
            ON frota_tipo_veiculo.cod_tipo = tipo_veiculo_vinculo.cod_tipo
    ";

    return $stSql;

}

}
?>
