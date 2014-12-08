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
    * Classe de Visao do Emitir Documento
    * Data de Criação   : 28/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Fellipe Esteves dos Santos

    * @package URBEM
    * @subpackage Visao

    $Id:$
*/
include_once( CAM_GT_FIS_MAPEAMENTO . "TFISPenalidade.class.php" );
include_once( CAM_GA_ADM_MAPEAMENTO . "TAdministracaoModeloDocumento.class.php" );

class VFISEmitirDocumento
{
    private $controller;
    private $visao;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function emitir($param)
    {
        return $this->controller->emitir( $param );
    }

    public function listar($param)
    {
        return $this->controller->listar( $param );
    }

}
?>
