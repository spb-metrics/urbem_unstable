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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: TFrotaVeiculoDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaVeiculoCessao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaVeiculoCessao()
    {
        parent::Persistente();
        $this->setTabela('frota.veiculo_cessao');
        $this->setCampoCod('id');
        $this->setComplementoChave('');

        $this->AddCampo('id'            ,'integer'  ,true  ,''  ,true,false);
        $this->AddCampo('cod_veiculo'   ,'integer'  ,true  ,''  ,false,true);
        $this->AddCampo('cod_processo'  ,'integer'  ,true  ,''  ,false,true);
        $this->AddCampo('exercicio'     ,'varchar'  ,true  ,'4' ,false,true);
        $this->AddCampo('cgm_cedente'   ,'integer'  ,true  ,''  ,false,true);
        $this->AddCampo('dt_inicio'     ,'date'     ,false ,''  ,false,false);
        $this->AddCampo('dt_termino'    ,'date'     ,false ,''  ,false,false);
    }

}